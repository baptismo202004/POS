<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\UnitType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        Brand::query()->firstOrCreate(
            ['brand_name' => 'Local'],
            ['status' => 'active', 'created_at' => $now, 'updated_at' => $now]
        );

        $brandsByName = Brand::query()->pluck('id', 'brand_name')->all();
        $categoriesByName = Category::query()->pluck('id', 'category_name')->all();
        $unitTypeIds = UnitType::query()->pluck('id')->all();
        $branchIds = Branch::query()->pluck('id')->all();

        if (empty($unitTypeIds)) {
            $this->command?->info('No unit types found. Please run UnitTypeSeeder first.');
            return;
        }

        if (empty($branchIds)) {
            $this->command?->info('No branches found. Please run BranchSeeder first.');
            return;
        }

        $hasBranchIdColumn = Schema::hasColumn('products', 'branch_id');
        $hasDescriptionColumn = Schema::hasColumn('products', 'description');
        $hasStockStatusColumn = Schema::hasColumn('products', 'stock_status');
        $hasSupplierIdColumn = Schema::hasColumn('products', 'supplier_id');


        $seedProducts = [
            ['product_name' => 'Coca-Cola 1.5L', 'barcode' => '1000000000001', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'Lucky Me Pancit Canton', 'barcode' => '1000000000002', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'Nescafe Classic 50g', 'barcode' => '1000000000003', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'Safeguard Soap 135g', 'barcode' => '1000000000004', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'Head & Shoulders Shampoo 180ml', 'barcode' => '1000000000005', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'Rice 5kg', 'barcode' => '1000000000009', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'Cooking Oil 1L', 'barcode' => '1000000000010', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'White Sugar 1kg', 'barcode' => '1000000000011', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'Iodized Salt 1kg', 'barcode' => '1000000000012', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'All-Purpose Flour 1kg', 'barcode' => '1000000000013', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'Laundry Detergent Powder 1kg', 'barcode' => '1000000000014', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'Dishwashing Liquid 500ml', 'barcode' => '1000000000015', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'Canned Sardines 155g', 'barcode' => '1000000000016', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'Canned Corned Beef 150g', 'barcode' => '1000000000017', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'Instant Coffee 25g', 'barcode' => '1000000000018', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'Bottled Water 500ml', 'barcode' => '1000000000019', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'Toothpaste 150g', 'barcode' => '1000000000020', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'Toothbrush', 'barcode' => '1000000000021', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'Shampoo Sachet', 'barcode' => '1000000000022', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'Body Soap Bar', 'barcode' => '1000000000023', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'Bread Loaf', 'barcode' => '1000000000024', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'Eggs 1 dozen', 'barcode' => '1000000000025', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'Fresh Milk 1L', 'barcode' => '1000000000026', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'Soy Sauce 1L', 'barcode' => '1000000000027', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'Vinegar 1L', 'barcode' => '1000000000028', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'Fish Sauce 500ml', 'barcode' => '1000000000029', 'category' => 'Groceries', 'brand' => 'Local'],
            ['product_name' => 'Chocolate Bar 50g', 'barcode' => '1000000000030', 'category' => 'Groceries', 'brand' => 'Local'],
        ];

        $defaultBranchId = (int) $branchIds[0];

        foreach ($seedProducts as $p) {
            $barcode = (string) $p['barcode'];

            $categoryId = null;
            if (! empty($p['category']) && isset($categoriesByName[$p['category']])) {
                $categoryId = (int) $categoriesByName[$p['category']];
            }

            $brandId = null;
            if (! empty($p['brand']) && isset($brandsByName[$p['brand']])) {
                $brandId = (int) $brandsByName[$p['brand']];
            }

            $productTypeId = 'non-electronic';

            $productData = [
                'product_name' => $p['product_name'],
                'barcode' => $barcode,
                'brand_id' => $brandId,
                'category_id' => $categoryId,
                'product_type_id' => $productTypeId,
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if ($hasDescriptionColumn) {
                $productData['description'] = null;
            }

            if ($hasBranchIdColumn) {
                $productData['branch_id'] = $defaultBranchId;
            }

            if ($hasStockStatusColumn) {
                $productData['stock_status'] = null;
            }

            if ($hasSupplierIdColumn) {
                $productData['supplier_id'] = null;
            }

            /** @var \App\Models\Product $product */
            $product = Product::query()->firstOrCreate(
                ['barcode' => $barcode],
                $productData
            );

            $assignBranches = [$defaultBranchId];
            DB::table('product_branch')->upsert(
                array_map(fn ($bid) => [
                    'product_id' => $product->id,
                    'branch_id' => $bid,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $assignBranches),
                ['product_id', 'branch_id'],
                ['updated_at']
            );

            DB::table('branch_stocks')->upsert([
                'branch_id' => $defaultBranchId,
                'product_id' => $product->id,
                'quantity_base' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ], ['branch_id', 'product_id'], ['updated_at']);

            $chosen = $this->pickUnitTypes($unitTypeIds);

            DB::table('product_unit_type')->where('product_id', $product->id)->delete();

            foreach ($chosen as $idx => $unitTypeId) {
                DB::table('product_unit_type')->insert([
                    'product_id' => $product->id,
                    'unit_type_id' => $unitTypeId,
                    'conversion_factor' => $idx === 0 ? 1 : ($idx + 1),
                    'is_base' => $idx === 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function pickUnitTypes(array $unitTypeIds): array
    {
        $count = count($unitTypeIds);
        if ($count === 1) {
            return [$unitTypeIds[0]];
        }

        $howMany = min(3, max(1, random_int(1, min(3, $count))));
        $pickedKeys = array_rand($unitTypeIds, $howMany);

        if (! is_array($pickedKeys)) {
            $pickedKeys = [$pickedKeys];
        }

        $picked = [];
        foreach ($pickedKeys as $k) {
            $picked[] = $unitTypeIds[$k];
        }

        return array_values(array_unique($picked));
    }
}
