<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            ['brand_name' => 'Apple', 'status' => 'active'],
            ['brand_name' => 'Samsung', 'status' => 'active'],
            ['brand_name' => 'Sony', 'status' => 'active'],
            ['brand_name' => 'LG', 'status' => 'active'],
            ['brand_name' => 'HP', 'status' => 'active'],
            ['brand_name' => 'Dell', 'status' => 'active'],
        ];

        foreach ($brands as $brand) {
            \App\Models\Brand::create($brand);
        }
    }
}
