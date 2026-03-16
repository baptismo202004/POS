<?php

namespace Database\Seeders\Pos;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::pluck('id')->all();
        $suppliers = Supplier::pluck('id')->all();
        $products = Product::pluck('id')->all();

        if (empty($branches) || empty($suppliers) || empty($products)) {
            return;
        }

        $now = now();

        DB::transaction(function () use ($branches, $suppliers, $products, $now) {
            $purchaseCount = 500;
            $targetItemCount = 2000;

            $itemsPerPurchase = (int) ceil($targetItemCount / $purchaseCount);
            $itemsPerPurchase = max(2, min(6, $itemsPerPurchase));

            for ($i = 1; $i <= $purchaseCount; $i++) {
                $purchaseAt = $now->copy()->subDays(random_int(1, 120))->setTime(random_int(8, 17), random_int(0, 59), random_int(0, 59));
                $branchId = $branches[array_rand($branches)];
                $supplierId = $suppliers[array_rand($suppliers)];

                $purchase = Purchase::create([
                    'branch_id' => $branchId,
                    'supplier_id' => $supplierId,
                    'cashier_id' => null,
                    'reference_number' => 'PO-' . strtoupper(Str::random(8)),
                    'total_cost' => 0,
                    'payment_status' => collect(['paid', 'pending'])->random(),
                    'purchase_date' => $purchaseAt->toDateString(),
                    'created_at' => $purchaseAt,
                    'updated_at' => $purchaseAt,
                ]);

                $productIds = collect($products)->random($itemsPerPurchase)->values()->all();

                $total = 0;
                foreach ($productIds as $productId) {
                    $qty = random_int(5, 50);
                    $baseUnit = $this->getBaseUnitTypeForProduct($productId);
                    if (!$baseUnit) {
                        continue;
                    }

                    $unitTypeId = (int) $baseUnit['unit_type_id'];
                    $factor = (float) ($baseUnit['conversion_factor'] ?? 1);
                    $factor = $factor > 0 ? $factor : 1;
                    $baseQty = (float) $qty * $factor;

                    $baseUnitCost = $this->generateBaseUnitCost($productId);
                    $subtotal = round($qty * $baseUnitCost, 2);

                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $productId,
                        'quantity' => $qty,
                        'unit_type_id' => $unitTypeId,
                        'unit_cost' => $baseUnitCost,
                        'subtotal' => $subtotal,
                        'created_at' => $purchaseAt,
                        'updated_at' => $purchaseAt,
                    ]);

                    DB::table('branch_stocks')->updateOrInsert(
                        ['product_id' => $productId, 'branch_id' => $branchId],
                        ['quantity_base' => 0, 'created_at' => $purchaseAt, 'updated_at' => $purchaseAt]
                    );

                    DB::table('branch_stocks')
                        ->where('product_id', $productId)
                        ->where('branch_id', $branchId)
                        ->update([
                            'quantity_base' => DB::raw('quantity_base + ' . (float) $baseQty),
                            'updated_at' => $purchaseAt,
                        ]);

                    DB::table('stock_movements')->insert([
                        'product_id' => $productId,
                        'branch_id' => $branchId,
                        'source_type' => 'purchases',
                        'source_id' => $purchase->id,
                        'movement_type' => 'purchase',
                        'quantity_base' => $baseQty,
                        'created_at' => $purchaseAt,
                    ]);

                    $total += $subtotal;
                }

                $purchase->update([
                    'total_cost' => round($total, 2),
                    'updated_at' => $purchaseAt,
                ]);

                $this->seedExpenseForPurchase($purchase->id, $supplierId, $branchId, $purchase->reference_number, $purchaseAt, $total);
            }
        });
    }

    private function getBaseUnitTypeForProduct(int $productId): ?array
    {
        $row = DB::table('product_unit_type')
            ->where('product_id', $productId)
            ->orderByDesc('is_base')
            ->orderBy('conversion_factor')
            ->first(['unit_type_id', 'conversion_factor', 'is_base']);

        if (!$row) {
            return null;
        }

        return [
            'unit_type_id' => (int) $row->unit_type_id,
            'conversion_factor' => (float) ($row->conversion_factor ?? 1),
            'is_base' => (bool) $row->is_base,
        ];
    }

    private function generateBaseUnitCost(int $productId): float
    {
        $barcode = (string) (DB::table('products')->where('id', $productId)->value('barcode') ?? '');

        if (str_starts_with($barcode, 'ELEC')) {
            return (float) random_int(250, 2500);
        }

        if (str_starts_with($barcode, 'COMP')) {
            return (float) random_int(200, 2000);
        }

        if (str_starts_with($barcode, 'BEV')) {
            return (float) random_int(10, 60);
        }

        if (str_starts_with($barcode, 'GROC')) {
            return (float) random_int(30, 120);
        }

        if (str_starts_with($barcode, 'HOME')) {
            return (float) random_int(8, 150);
        }

        return (float) random_int(20, 500);
    }

    private function seedStockInUnitPrices(int $productId, int $stockInId, float $basePrice, $at): void
    {
        $unitRows = DB::table('product_unit_type')
            ->where('product_id', $productId)
            ->orderByDesc('is_base')
            ->get(['unit_type_id', 'conversion_factor', 'is_base']);

        if ($unitRows->isEmpty()) {
            return;
        }

        $rows = [];
        foreach ($unitRows as $row) {
            $factor = (float) ($row->conversion_factor ?? 1);
            $factor = $factor > 0 ? $factor : 1;

            // Pricing model:
            // - Base unit gets a small retail margin over cost
            // - Larger units (packs/cases) have cheaper per-base-unit price (bulk discount)
            // - Smaller units (if any) have higher per-base-unit price (convenience premium)
            $isBase = (bool) $row->is_base;

            $baseRetail = $basePrice * (1 + (random_int(15, 35) / 100));
            $unitPrice = $baseRetail * $factor;

            if (!$isBase && $factor > 1) {
                $unitPrice *= (1 - (random_int(3, 12) / 100));
            }

            $rows[] = [
                'stock_in_id' => $stockInId,
                'unit_type_id' => (int) $row->unit_type_id,
                'price' => round($unitPrice, 2),
                'created_at' => $at,
                'updated_at' => $at,
            ];
        }

        DB::table('stock_in_unit_prices')->upsert(
            $rows,
            ['stock_in_id', 'unit_type_id'],
            ['price', 'updated_at']
        );
    }

    private function seedExpenseForPurchase(int $purchaseId, int $supplierId, int $branchId, ?string $reference, $at, float $amount): void
    {
        $expenseCategoryId = DB::table('expense_categories')->where('name', 'Purchases')->value('id');
        if (!$expenseCategoryId) {
            return;
        }

        DB::table('expenses')->insert([
            'expense_category_id' => $expenseCategoryId,
            'supplier_id' => $supplierId,
            'branch_id' => $branchId,
            'purchase_id' => $purchaseId,
            'reference_number' => $reference,
            'description' => 'Purchase expense for ' . ($reference ?? ('PO-' . $purchaseId)),
            'amount' => round($amount, 2),
            'expense_date' => $at->toDateString(),
            'payment_method' => collect(['cash', 'bank_transfer', 'card'])->random(),
            'receipt_path' => null,
            'created_at' => $at,
            'updated_at' => $at,
        ]);
    }
}
