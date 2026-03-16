<?php

namespace Database\Seeders\Pos;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SalesSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::pluck('id')->all();
        $products = Product::pluck('id')->all();
        $cashierId = DB::table('users')->orderBy('id')->value('id');

        if (empty($branches) || empty($products) || !$cashierId) {
            return;
        }

        $now = now();

        DB::transaction(function () use ($branches, $products, $cashierId, $now) {
            $saleCount = 2000;

            for ($i = 1; $i <= $saleCount; $i++) {
                $at = $now->copy()->subDays(random_int(0, 60))->setTime(random_int(8, 21), random_int(0, 59), random_int(0, 59));

                $branchId = $branches[array_rand($branches)];

                $sale = Sale::create([
                    'reference_number' => 'SI-' . strtoupper(Str::random(10)),
                    'receipt_group_id' => null,
                    'cashier_id' => $cashierId,
                    'employee_id' => (string) $cashierId,
                    'customer_id' => null,
                    'branch_id' => $branchId,
                    'total_amount' => 0,
                    'tax' => 0,
                    'payment_method' => collect(['cash', 'card', 'credit'])->random(),
                    'status' => 'completed',
                    'voided' => false,
                    'voided_by' => null,
                    'voided_at' => null,
                    'created_at' => $at,
                    'updated_at' => $at,
                ]);

                $itemCount = random_int(1, 4);
                $productIds = collect($products)->random($itemCount)->values()->all();

                $total = 0;
                foreach ($productIds as $productId) {
                    $unitTypeId = $this->pickUnitTypeForBranchProduct($productId, $branchId);
                    if (!$unitTypeId) {
                        continue;
                    }

                    $available = InventoryHelper::availableStock($productId, $branchId, $unitTypeId);
                    if ($available <= 0) {
                        continue;
                    }

                    $qty = random_int(1, min(3, $available));

                    $unitPrice = $this->getLatestUnitPrice($productId, $branchId, $unitTypeId);
                    if ($unitPrice <= 0) {
                        $unitPrice = (float) random_int(50, 1000);
                    }

                    $subtotal = round($unitPrice * $qty, 2);

                    InventoryHelper::deductStockForSale($sale->id, $productId, $branchId, $unitTypeId, (float) $qty, $at);

                    $saleItemPayload = [
                        'sale_id' => $sale->id,
                        'product_id' => $productId,
                        'unit_type_id' => $unitTypeId,
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                        'created_at' => $at,
                        'updated_at' => $at,
                    ];

                    SaleItem::create($saleItemPayload);

                    $total += $subtotal;
                }

                if ($total <= 0) {
                    $sale->delete();
                    continue;
                }

                $sale->update([
                    'total_amount' => round($total, 2),
                    'updated_at' => $at,
                ]);
            }
        });
    }

    private function pickUnitTypeForBranchProduct(int $productId, int $branchId): ?int
    {
        $unitTypeId = \App\Models\StockIn::query()
            ->where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->orderByDesc('id')
            ->value('unit_type_id');

        if ($unitTypeId) {
            return (int) $unitTypeId;
        }

        $fallback = DB::table('product_unit_type')->where('product_id', $productId)->orderByDesc('is_base')->value('unit_type_id');
        return $fallback ? (int) $fallback : null;
    }

    private function getLatestUnitPrice(int $productId, int $branchId, int $unitTypeId): float
    {
        $stockInId = \App\Models\StockIn::query()
            ->where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->where('unit_type_id', $unitTypeId)
            ->orderByDesc('id')
            ->value('id');

        if (!$stockInId) {
            return 0;
        }

        $price = DB::table('stock_in_unit_prices')
            ->where('stock_in_id', $stockInId)
            ->where('unit_type_id', $unitTypeId)
            ->value('price');

        if (!is_null($price)) {
            return (float) $price;
        }

        $fallback = \App\Models\StockIn::query()->where('id', $stockInId)->value('price');
        return $fallback ? (float) $fallback : 0;
    }
}
