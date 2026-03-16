<?php

namespace Database\Seeders\Pos;

use App\Models\Branch;
use App\Models\Product;
use App\Models\StockIn;
use App\Models\StockTransfer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::pluck('id')->all();
        $products = Product::pluck('id')->all();

        if (count($branches) < 2 || empty($products)) {
            return;
        }

        $now = now();

        DB::transaction(function () use ($branches, $products, $now) {
            $this->seedAdjustments($branches, $products, $now);
            $this->seedTransfers($branches, $products, $now);
        });
    }

    private function seedAdjustments(array $branches, array $products, $now): void
    {
        $adjustmentCount = 200;

        for ($i = 0; $i < $adjustmentCount; $i++) {
            $at = $now->copy()->subDays(random_int(1, 90))->setTime(random_int(8, 18), random_int(0, 59), random_int(0, 59));

            $branchId = $branches[array_rand($branches)];
            $productId = $products[array_rand($products)];

            $lot = StockIn::query()
                ->where('branch_id', $branchId)
                ->where('product_id', $productId)
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            if (!$lot) {
                continue;
            }

            $available = (int) ($lot->quantity - $lot->sold);
            if ($available <= 0) {
                continue;
            }

            $reduceBy = random_int(1, min(3, $available));

            $lot->sold = (int) $lot->sold + $reduceBy;
            $lot->save();

            DB::table('stock_movements')->insert([
                'product_id' => $productId,
                'branch_id' => $branchId,
                'product_serial_id' => null,
                'movement_type' => 'adjustment',
                'quantity' => -$reduceBy,
                'reference_id' => $lot->id,
                'created_at' => $at,
                'updated_at' => $at,
            ]);
        }
    }

    private function seedTransfers(array $branches, array $products, $now): void
    {
        $transferCount = 200;

        for ($i = 0; $i < $transferCount; $i++) {
            $at = $now->copy()->subDays(random_int(1, 90))->setTime(random_int(8, 18), random_int(0, 59), random_int(0, 59));

            $from = $branches[array_rand($branches)];
            $to = $branches[array_rand($branches)];
            if ($from === $to) {
                continue;
            }

            $productId = $products[array_rand($products)];

            $fromLot = StockIn::query()
                ->where('branch_id', $from)
                ->where('product_id', $productId)
                ->whereColumn('quantity', '>', 'sold')
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->first();

            if (!$fromLot) {
                continue;
            }

            $available = (int) ($fromLot->quantity - $fromLot->sold);
            if ($available <= 0) {
                continue;
            }

            $qty = random_int(1, min(10, $available));

            $transfer = StockTransfer::create([
                'product_id' => $productId,
                'from_branch_id' => $from,
                'to_branch_id' => $to,
                'quantity' => $qty,
                'status' => 'approved',
                'notes' => 'Inter-branch transfer',
                'created_at' => $at,
                'updated_at' => $at,
            ]);

            if (Schema::hasColumn('stock_transfers', 'transferred_by')) {
                $transfer->transferred_by = null;
                $transfer->save();
            }

            $fromLot->sold = (int) $fromLot->sold + $qty;
            $fromLot->save();

            $toLot = StockIn::create([
                'product_id' => $productId,
                'branch_id' => $to,
                'purchase_id' => null,
                'unit_type_id' => $fromLot->unit_type_id,
                'quantity' => $qty,
                'price' => $fromLot->price,
                'sold' => 0,
                'created_at' => $at,
                'updated_at' => $at,
            ]);

            DB::table('stock_movements')->insert([
                [
                    'product_id' => $productId,
                    'branch_id' => $from,
                    'product_serial_id' => null,
                    'movement_type' => 'transfer',
                    'quantity' => -$qty,
                    'reference_id' => $transfer->id,
                    'created_at' => $at,
                    'updated_at' => $at,
                ],
                [
                    'product_id' => $productId,
                    'branch_id' => $to,
                    'product_serial_id' => null,
                    'movement_type' => 'transfer',
                    'quantity' => $qty,
                    'reference_id' => $transfer->id,
                    'created_at' => $at,
                    'updated_at' => $at,
                ],
            ]);

            $this->copyUnitPricesIfAny($fromLot->id, $toLot->id, $at);
        }
    }

    private function copyUnitPricesIfAny(int $fromStockInId, int $toStockInId, $at): void
    {
        $rows = DB::table('stock_in_unit_prices')
            ->where('stock_in_id', $fromStockInId)
            ->get(['unit_type_id', 'price']);

        if ($rows->isEmpty()) {
            return;
        }

        $insert = [];
        foreach ($rows as $r) {
            $insert[] = [
                'stock_in_id' => $toStockInId,
                'unit_type_id' => (int) $r->unit_type_id,
                'price' => (float) $r->price,
                'created_at' => $at,
                'updated_at' => $at,
            ];
        }

        DB::table('stock_in_unit_prices')->upsert(
            $insert,
            ['stock_in_id', 'unit_type_id'],
            ['price', 'updated_at']
        );
    }
}
