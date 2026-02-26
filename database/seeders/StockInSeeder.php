<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
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

class StockInSeeder extends Seeder
{
    public function run(): void
    {
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

        // Create suppliers if they don't exist
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
        $supplierC = Supplier::firstOrCreate(
            ['supplier_name' => 'Tech Solutions'],
            [
                'contact_person' => 'Mike Johnson',
                'email' => 'tech@example.com',
                'phone' => '555-0303',
                'address' => 'Technology Hub',
                'status' => 'active',
            ]
        );

        $brands = Brand::pluck('id')->all();
        $categories = Category::pluck('id')->all();
        $productTypes = ProductType::pluck('id')->all();
        $unitTypes = UnitType::pluck('id')->all();

        // Get existing products or create sample ones
        $products = Product::all();
        
        if ($products->isEmpty()) {
            // Create sample products if none exist
            for ($i = 1; $i <= 30; $i++) {
                $products[] = Product::firstOrCreate(
                    ['product_name' => 'Sample Product ' . $i],
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
        }

        $purchaseSuppliers = [$supplierA->id, $supplierB->id, $supplierC->id];

        // Create purchases with stock-ins
        for ($p = 1; $p <= 10; $p++) {
            $itemsCount = random_int(3, 8);
            $selectedProducts = collect($products)->random($itemsCount);

            $purchase = Purchase::create([
                'supplier_id' => $purchaseSuppliers[array_rand($purchaseSuppliers)],
                'reference_number' => 'PO-' . strtoupper(Str::random(6)),
                'total_cost' => 0,
                'payment_status' => 'paid',
                'purchase_date' => Carbon::now()->subDays(random_int(1, 30)),
            ]);

            $total = 0;
            foreach ($selectedProducts as $prod) {
                $qty = random_int(5, 50);
                $unitCost = random_int(50, 2000) / 1.0;
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
                    'branch_id' => $branchIds[array_rand($branchIds)],
                    'purchase_id' => $purchase->id,
                    'unit_type_id' => $unitTypeId,
                    'quantity' => $qty,
                    'initial_quantity' => $qty,
                    'price' => $unitCost,
                    'sold' => random_int(0, $qty * 0.3), // Some items already sold
                ]);

                $total += $subtotal;
            }

            $purchase->update(['total_cost' => $total]);
        }

        // Create additional stock-ins without purchases (direct stock-ins)
        foreach (collect($products)->random(15) as $prod) {
            $unitTypeId = $unitTypes ? $unitTypes[array_rand($unitTypes)] : null;
            StockIn::create([
                'product_id' => $prod->id,
                'branch_id' => $branchIds[array_rand($branchIds)],
                'purchase_id' => null,
                'unit_type_id' => $unitTypeId,
                'quantity' => random_int(1, 20),
                'initial_quantity' => random_int(1, 20),
                'price' => random_int(80, 500) / 1.0,
                'sold' => random_int(0, 5),
            ]);
        }

        // Create some stock-ins with different statuses
        foreach (collect($products)->random(10) as $prod) {
            $unitTypeId = $unitTypes ? $unitTypes[array_rand($unitTypes)] : null;
            StockIn::create([
                'product_id' => $prod->id,
                'branch_id' => $branchIds[array_rand($branchIds)],
                'purchase_id' => null,
                'unit_type_id' => $unitTypeId,
                'quantity' => random_int(10, 100),
                'initial_quantity' => random_int(10, 100),
                'price' => random_int(100, 1000) / 1.0,
                'sold' => random_int(0, 50),
            ]);
        }

        $this->command->info('StockInSeeder completed successfully!');
        $this->command->info('Created ' . StockIn::count() . ' stock-in records');
    }
}
