<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\UnitType;
use Illuminate\Support\Facades\DB;

class FixProductUnitTypesSeeder extends Seeder
{
    public function run(): void
    {
        // Get all unit types
        $unitTypes = UnitType::all();
        
        if ($unitTypes->isEmpty()) {
            $this->command->info('No unit types found. Please run UnitTypeSeeder first.');
            return;
        }
        
        // Get all products that don't have unit types
        $productsWithoutUnitTypes = Product::whereDoesntHave('unitTypes')->get();
        
        foreach ($productsWithoutUnitTypes as $product) {
            // Assign 1-3 random unit types to each product
            $randomUnitTypes = $unitTypes->random(rand(1, min(3, $unitTypes->count())));
            
            foreach ($randomUnitTypes as $unitType) {
                // Insert into pivot table
                DB::table('product_unit_type')->insert([
                    'product_id' => $product->id,
                    'unit_type_id' => $unitType->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            $this->command->info("Assigned unit types to product: {$product->product_name}");
        }
        
        $this->command->info('Fixed unit types for ' . $productsWithoutUnitTypes->count() . ' products.');
    }
}
