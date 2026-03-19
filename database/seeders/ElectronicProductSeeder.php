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

class ElectronicProductSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        Brand::query()->firstOrCreate(
            ['brand_name' => 'Generic'],
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

        $electronicsCategoryId = null;
        if (isset($categoriesByName['Electronics'])) {
            $electronicsCategoryId = (int) $categoriesByName['Electronics'];
        }

        $hasBranchIdColumn = Schema::hasColumn('products', 'branch_id');
        $hasDescriptionColumn = Schema::hasColumn('products', 'description');

        $seedProducts = [
            [
                'product_name' => 'Smartphone Android 128GB',
                'barcode' => '2000000000001',
                'brand' => 'Samsung',
                'model_number' => 'A128',
                'warranty_type' => 'manufacturer',
                'warranty_coverage_months' => 12,
                'voltage_specs' => '5V',
            ],
            [
                'product_name' => 'Smartphone iOS 128GB',
                'barcode' => '2000000000002',
                'brand' => 'Apple',
                'model_number' => 'IP128',
                'warranty_type' => 'manufacturer',
                'warranty_coverage_months' => 12,
                'voltage_specs' => '5V',
            ],
            [
                'product_name' => 'LED TV 43-inch',
                'barcode' => '2000000000003',
                'brand' => 'Sony',
                'model_number' => 'TV43',
                'warranty_type' => 'manufacturer',
                'warranty_coverage_months' => 24,
                'voltage_specs' => '110-220V',
            ],
            [
                'product_name' => 'Bluetooth Speaker',
                'barcode' => '2000000000004',
                'brand' => 'LG',
                'model_number' => 'SPK01',
                'warranty_type' => 'shop',
                'warranty_coverage_months' => 6,
                'voltage_specs' => '5V',
            ],
            [
                'product_name' => 'Laptop 15-inch 8GB/256GB',
                'barcode' => '2000000000005',
                'brand' => 'Dell',
                'model_number' => 'LTP15',
                'warranty_type' => 'manufacturer',
                'warranty_coverage_months' => 12,
                'voltage_specs' => '110-220V',
            ],
            [
                'product_name' => 'Power Bank 10000mAh',
                'barcode' => '2000000000006',
                'brand' => 'Generic',
                'model_number' => 'PB10K',
                'warranty_type' => 'shop',
                'warranty_coverage_months' => 3,
                'voltage_specs' => '5V',
            ],
            [
                'product_name' => 'USB Charger 20W',
                'barcode' => '2000000000007',
                'brand' => 'Generic',
                'model_number' => 'CH20W',
                'warranty_type' => 'shop',
                'warranty_coverage_months' => 3,
                'voltage_specs' => '110-220V',
            ],
            [
                'product_name' => 'Earphones Wired',
                'barcode' => '2000000000008',
                'brand' => 'Generic',
                'model_number' => 'EAR01',
                'warranty_type' => 'none',
                'warranty_coverage_months' => null,
                'voltage_specs' => null,
            ],
        ];

        $defaultBranchId = (int) $branchIds[0];

        foreach ($seedProducts as $p) {
            $barcode = (string) $p['barcode'];

            $brandId = null;
            if (! empty($p['brand']) && isset($brandsByName[$p['brand']])) {
                $brandId = (int) $brandsByName[$p['brand']];
            }

            $productData = [
                'product_name' => $p['product_name'],
                'barcode' => $barcode,
                'brand_id' => $brandId,
                'category_id' => $electronicsCategoryId,
                'product_type_id' => 'electronic',
                'model_number' => $p['model_number'] ?? null,
                'warranty_type' => $p['warranty_type'] ?? 'none',
                'warranty_coverage_months' => $p['warranty_coverage_months'] ?? null,
                'voltage_specs' => $p['voltage_specs'] ?? null,
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

            $product = Product::query()->updateOrCreate(
                ['barcode' => $barcode],
                $productData
            );

            DB::table('product_branch')->upsert(
                [[
                    'product_id' => $product->id,
                    'branch_id' => $defaultBranchId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]],
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
