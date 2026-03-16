<?php

namespace Database\Seeders\Pos;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Supplier;
use App\Models\UnitType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BaseDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::transaction(function () use ($now) {
            $this->seedSuppliers($now);
            $this->seedProducts($now);
            $this->attachProductsToBranches($now);
            $this->seedProductUnitTypes($now);
            $this->seedCustomers($now);
        });
    }

    private function seedSuppliers($now): void
    {
        $suppliers = [];
        for ($i = 1; $i <= 10; $i++) {
            $suppliers[] = [
                'supplier_name' => "Supplier {$i} Trading",
                'contact_person' => "Contact {$i}",
                'email' => "supplier{$i}@example.com",
                'phone' => '09' . random_int(100000000, 999999999),
                'address' => "Warehouse Zone {$i}",
                'status' => 'active',
                'created_at' => $now->copy()->subMonths(random_int(8, 24)),
                'updated_at' => $now->copy()->subDays(random_int(1, 30)),
            ];
        }

        Supplier::upsert($suppliers, ['email'], ['supplier_name', 'contact_person', 'phone', 'address', 'status', 'updated_at']);
    }

    private function seedProducts($now): void
    {
        $brands = Brand::pluck('id')->all();
        $categories = Category::pluck('id')->all();
        $productTypes = ProductType::pluck('id')->all();

        $categoryByName = Category::pluck('id', 'category_name')->all();
        $brandByName = Brand::pluck('id', 'brand_name')->all();

        $products = [];

        $catalog = [
            // Groceries (weight)
            ['name' => 'Rice - Premium (1kg)', 'barcode' => 'GROC00000001', 'category' => 'Groceries', 'brand' => null, 'min' => 10, 'max' => 300],
            ['name' => 'Sugar - White (1kg)', 'barcode' => 'GROC00000002', 'category' => 'Groceries', 'brand' => null, 'min' => 10, 'max' => 250],
            ['name' => 'Salt - Iodized (1kg)', 'barcode' => 'GROC00000003', 'category' => 'Groceries', 'brand' => null, 'min' => 10, 'max' => 250],

            // Groceries (beverages)
            ['name' => 'Softdrink Cola (330ml)', 'barcode' => 'BEV00000001', 'category' => 'Groceries', 'brand' => 'Samsung', 'min' => 20, 'max' => 400],
            ['name' => 'Bottled Water (500ml)', 'barcode' => 'BEV00000002', 'category' => 'Groceries', 'brand' => null, 'min' => 30, 'max' => 500],
            ['name' => 'Instant Coffee (Sachet)', 'barcode' => 'BEV00000003', 'category' => 'Groceries', 'brand' => null, 'min' => 50, 'max' => 800],

            // Toiletries / household
            ['name' => 'Bath Soap Bar (90g)', 'barcode' => 'HOME00000001', 'category' => 'Groceries', 'brand' => null, 'min' => 20, 'max' => 300],
            ['name' => 'Shampoo (12ml Sachet)', 'barcode' => 'HOME00000002', 'category' => 'Groceries', 'brand' => null, 'min' => 60, 'max' => 1000],
            ['name' => 'Toothpaste (150g)', 'barcode' => 'HOME00000003', 'category' => 'Groceries', 'brand' => null, 'min' => 20, 'max' => 400],
            ['name' => 'Tissue Roll - 2ply', 'barcode' => 'HOME00000004', 'category' => 'Groceries', 'brand' => null, 'min' => 30, 'max' => 500],

            // Electronics
            ['name' => 'USB Cable (1m)', 'barcode' => 'ELEC00000001', 'category' => 'Electronics', 'brand' => 'Samsung', 'min' => 5, 'max' => 200],
            ['name' => 'Phone Charger (20W)', 'barcode' => 'ELEC00000002', 'category' => 'Electronics', 'brand' => 'Apple', 'min' => 5, 'max' => 150],
            ['name' => 'Powerbank (10000mAh)', 'barcode' => 'ELEC00000003', 'category' => 'Electronics', 'brand' => 'Sony', 'min' => 3, 'max' => 80],

            // Computers
            ['name' => 'Wireless Mouse', 'barcode' => 'COMP00000001', 'category' => 'Computers', 'brand' => 'Dell', 'min' => 5, 'max' => 120],
            ['name' => 'Keyboard - USB', 'barcode' => 'COMP00000002', 'category' => 'Computers', 'brand' => 'HP', 'min' => 5, 'max' => 100],
            ['name' => 'HDMI Cable (2m)', 'barcode' => 'COMP00000003', 'category' => 'Computers', 'brand' => 'LG', 'min' => 5, 'max' => 120],
        ];

        $fallbackCategoryId = $categories ? $categories[array_rand($categories)] : null;
        $fallbackProductTypeId = $productTypes ? $productTypes[array_rand($productTypes)] : null;

        foreach ($catalog as $idx => $item) {
            $categoryId = $categoryByName[$item['category']] ?? $fallbackCategoryId;
            $brandId = $item['brand'] ? ($brandByName[$item['brand']] ?? null) : null;

            $products[] = [
                'product_name' => $item['name'],
                'barcode' => $item['barcode'],
                'brand_id' => $brandId,
                'category_id' => $categoryId,
                'product_type_id' => $fallbackProductTypeId,
                'model_number' => 'MDL-' . Str::upper(Str::random(6)),
                'image' => null,
                'tracking_type' => 'none',
                'warranty_type' => 'none',
                'warranty_coverage_months' => null,
                'voltage_specs' => null,
                'status' => 'active',
                'low_stock_threshold' => max(5, (int) (($item['min'] ?? 5) * 0.6)),
                'min_stock_level' => (int) ($item['min'] ?? 5),
                'max_stock_level' => (int) ($item['max'] ?? 100),
                'created_at' => $now->copy()->subMonths(random_int(6, 24)),
                'updated_at' => $now->copy()->subDays(random_int(1, 30)),
            ];
        }

        Product::upsert($products, ['barcode'], [
            'product_name',
            'brand_id',
            'category_id',
            'product_type_id',
            'model_number',
            'image',
            'tracking_type',
            'warranty_type',
            'warranty_coverage_months',
            'voltage_specs',
            'status',
            'low_stock_threshold',
            'min_stock_level',
            'max_stock_level',
            'updated_at',
        ]);
    }

    private function attachProductsToBranches($now): void
    {
        $products = Product::pluck('id')->all();
        $branches = Branch::pluck('id')->all();

        if (empty($products) || empty($branches)) {
            return;
        }

        $rows = [];
        foreach ($products as $productId) {
            foreach ($branches as $branchId) {
                $rows[] = [
                    'product_id' => $productId,
                    'branch_id' => $branchId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('product_branch')->upsert(
            $rows,
            ['product_id', 'branch_id'],
            ['updated_at']
        );
    }

    private function seedProductUnitTypes($now): void
    {
        $unitIdByName = UnitType::pluck('id', 'unit_name')->all();

        $pc = $unitIdByName['Piece (pc)'] ?? null;
        $pack = $unitIdByName['Pack'] ?? null;
        $box = $unitIdByName['Box'] ?? null;
        $case = $unitIdByName['Case'] ?? null;
        $bottle = $unitIdByName['Bottle'] ?? null;
        $can = $unitIdByName['Can'] ?? null;
        $sachet = $unitIdByName['Sachet'] ?? null;
        $roll = $unitIdByName['Roll'] ?? null;
        $g = $unitIdByName['Gram (g)'] ?? null;
        $kg = $unitIdByName['Kilogram (kg)'] ?? null;
        $ml = $unitIdByName['Milliliter (ml)'] ?? null;
        $l = $unitIdByName['Liter (L)'] ?? null;

        $products = Product::all(['id', 'barcode', 'product_name']);
        if ($products->isEmpty()) {
            return;
        }

        $rows = [];
        foreach ($products as $p) {
            $barcode = (string) $p->barcode;

            $mapping = [];
            if (str_starts_with($barcode, 'GROC') || str_starts_with($barcode, 'HOME')) {
                if (str_contains($p->product_name, 'Rice') || str_contains($p->product_name, 'Sugar') || str_contains($p->product_name, 'Salt')) {
                    if ($kg && $g) {
                        $mapping[] = ['unit' => $kg, 'factor' => 1, 'base' => true];
                        $mapping[] = ['unit' => $g, 'factor' => 0.001, 'base' => false];
                    }
                } elseif (str_contains($p->product_name, '330ml') || str_contains($p->product_name, '500ml')) {
                    if ($bottle && $case) {
                        $mapping[] = ['unit' => $bottle, 'factor' => 1, 'base' => true];
                        $mapping[] = ['unit' => $case, 'factor' => 24, 'base' => false];
                    }
                } elseif (str_contains($p->product_name, 'Sachet')) {
                    if ($sachet && $pack) {
                        $mapping[] = ['unit' => $sachet, 'factor' => 1, 'base' => true];
                        $mapping[] = ['unit' => $pack, 'factor' => 10, 'base' => false];
                    }
                } elseif (str_contains($p->product_name, 'Roll')) {
                    if ($roll && $pack) {
                        $mapping[] = ['unit' => $roll, 'factor' => 1, 'base' => true];
                        $mapping[] = ['unit' => $pack, 'factor' => 6, 'base' => false];
                    }
                } else {
                    if ($pc && $pack) {
                        $mapping[] = ['unit' => $pc, 'factor' => 1, 'base' => true];
                        $mapping[] = ['unit' => $pack, 'factor' => 3, 'base' => false];
                    }
                }
            } else {
                if ($pc && $box) {
                    $mapping[] = ['unit' => $pc, 'factor' => 1, 'base' => true];
                    $mapping[] = ['unit' => $box, 'factor' => 5, 'base' => false];
                }
            }

            foreach ($mapping as $m) {
                if (!$m['unit']) {
                    continue;
                }
                $rows[] = [
                    'product_id' => (int) $p->id,
                    'unit_type_id' => (int) $m['unit'],
                    'conversion_factor' => (float) $m['factor'],
                    'is_base' => (bool) $m['base'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('product_unit_type')->upsert(
            $rows,
            ['product_id', 'unit_type_id'],
            ['conversion_factor', 'is_base', 'updated_at']
        );
    }

    private function seedCustomers($now): void
    {
        $creatorId = DB::table('users')->orderBy('id')->value('id');

        $customers = [];
        for ($i = 1; $i <= 200; $i++) {
            $customers[] = [
                'full_name' => "Customer {$i}",
                'phone' => '09' . random_int(100000000, 999999999),
                'email' => "customer{$i}@example.com",
                'max_credit_limit' => random_int(1000, 15000),
                'status' => 'active',
                'created_by' => $creatorId,
                'address' => "Address Line {$i}",
                'created_at' => $now->copy()->subMonths(random_int(1, 18)),
                'updated_at' => $now->copy()->subDays(random_int(1, 30)),
            ];
        }

        Customer::upsert($customers, ['email'], ['full_name', 'phone', 'max_credit_limit', 'status', 'created_by', 'address', 'updated_at']);
    }
}
