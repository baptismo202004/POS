<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserType;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductType;
use App\Models\UnitType;
use App\Models\Branch;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockIn;
use App\Models\Expense;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $cashierType = UserType::where('name', 'Cashier')->value('id') ?? 2;

        $maxEmp = DB::table('users')->selectRaw("MAX(CAST(SUBSTRING(employee_id, 4) AS UNSIGNED)) as maxnum")->value('maxnum');
        $nextNumber = $maxEmp ? (int)$maxEmp : 0;

        for ($i = 1; $i <= 5; $i++) {
            $nextNumber++;
            $employeeId = sprintf('EMP%05d', $nextNumber);
            User::firstOrCreate(
                ['email' => "cashier{$i}@example.com"],
                [
                    'employee_id' => $employeeId,
                    'name' => "Cashier {$i}",
                    'password' => Hash::make('password'),
                    'user_type_id' => $cashierType,
                    'status' => 'active',
                ]
            );
        }

        // Get all available branches
        $branches = Branch::all();
        
        // If no branches exist, create the default ones
        if ($branches->isEmpty()) {
            $branches = collect([
                Branch::firstOrCreate(['branch_name' => 'Main Branch'], [
                    'address' => 'Lanas City of Naga, Cebu',
                    'assign_to' => null,
                    'status' => 'active',
                ]),
                Branch::firstOrCreate(['branch_name' => 'RK'], [
                    'address' => 'Lanas Elementary School',
                    'assign_to' => null,
                    'status' => 'active',
                ]),
                Branch::firstOrCreate(['branch_name' => 'MCS'], [
                    'address' => 'Lanas City of Naga, Cebu',
                    'assign_to' => null,
                    'status' => 'active',
                ]),
            ]);
        }
        
        $branchIds = $branches->pluck('id')->toArray();

        $supplierA = Supplier::firstOrCreate(
            ['supplier_name' => 'Acme Supplies'],
            [
                'contact_person' => 'John Doe',
                'email' => 'acme@example.com',
                'phone' => '555-0101',
                'address' => 'Industrial Park',
                'status' => 'active',
            ]
        );
        $supplierB = Supplier::firstOrCreate(
            ['supplier_name' => 'Globex Traders'],
            [
                'contact_person' => 'Jane Smith',
                'email' => 'globex@example.com',
                'phone' => '555-0202',
                'address' => 'Commerce Ave',
                'status' => 'active',
            ]
        );

        $brands = Brand::pluck('id')->all();
        $categories = Category::pluck('id')->all();
        $productTypes = ProductType::pluck('id')->all();
        $unitTypes = UnitType::pluck('id')->all();

        $products = [];
        for ($i = 1; $i <= 20; $i++) {
            $products[] = Product::firstOrCreate(
                ['product_name' => 'Test Product ' . $i],
                [
                    'barcode' => strtoupper(Str::random(10)) . $i,
                    'brand_id' => $brands ? $brands[array_rand($brands)] : null,
                    'category_id' => $categories ? $categories[array_rand($categories)] : null,
                    'product_type_id' => $productTypes ? $productTypes[array_rand($productTypes)] : null,
                    'model_number' => 'MDL-' . Str::upper(Str::random(5)),
                    'image' => null,
                    'tracking_type' => 'none',
                    'warranty_type' => 'none',
                    'warranty_coverage_months' => null,
                    'voltage_specs' => null,
                    'status' => 'active',
                ]
            );
        }

        $purchaseSuppliers = [$supplierA->id, $supplierB->id];
        $expensePurchaseCategoryId = DB::table('expense_categories')->where('name', 'Purchases')->value('id');

        for ($p = 1; $p <= 3; $p++) {
            $itemsCount = random_int(3, 6);
            $selectedProducts = collect($products)->random($itemsCount);

            $purchase = Purchase::create([
                'supplier_id' => $purchaseSuppliers[array_rand($purchaseSuppliers)],
                'reference_number' => 'PO-' . strtoupper(Str::random(6)),
                'total_cost' => 0,
                'payment_status' => 'paid',
                'purchase_date' => Carbon::now()->subDays(10 - $p),
            ]);

            $total = 0;
            foreach ($selectedProducts as $prod) {
                $qty = random_int(5, 20);
                $unitCost = random_int(100, 1000) / 1.0;
                $unitTypeId = $unitTypes ? $unitTypes[array_rand($unitTypes)] : null;
                $subtotal = $qty * $unitCost;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $prod->id,
                    'quantity' => $qty,
                    'unit_type_id' => $unitTypeId,
                    'unit_cost' => $unitCost,
                    'subtotal' => $subtotal,
                ]);

                StockIn::create([
                    'product_id' => $prod->id,
                    'branch_id' => $branchIds[array_rand($branchIds)], // Random branch assignment
                    'purchase_id' => $purchase->id,
                    'unit_type_id' => $unitTypeId,
                    'quantity' => $qty,
                    'initial_quantity' => $qty,
                    'price' => $unitCost,
                    'sold' => 0,
                ]);

                $total += $subtotal;
            }

            $purchase->update(['total_cost' => $total]);

            if ($expensePurchaseCategoryId) {
                Expense::create([
                    'expense_category_id' => $expensePurchaseCategoryId,
                    'supplier_id' => $purchase->supplier_id,
                    'purchase_id' => $purchase->id,
                    'reference_number' => $purchase->reference_number,
                    'description' => 'Purchase expense for ' . $purchase->reference_number,
                    'amount' => $total,
                    'expense_date' => $purchase->purchase_date,
                    'payment_method' => 'cash',
                    'receipt_path' => null,
                ]);
            }
        }

        foreach (collect($products)->random(5) as $prod) {
            $unitTypeId = $unitTypes ? $unitTypes[array_rand($unitTypes)] : null;
            StockIn::create([
                'product_id' => $prod->id,
                'branch_id' => $branchIds[array_rand($branchIds)], // Random branch assignment
                'purchase_id' => null,
                'unit_type_id' => $unitTypeId,
                'quantity' => random_int(1, 5),
                'initial_quantity' => random_int(1, 5),
                'price' => random_int(80, 200) / 1.0,
                'sold' => 0,
            ]);
        }
    }
}
