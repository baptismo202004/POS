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
        $now = now();

        $brands = [
            ['brand_name' => 'Apple', 'status' => 'active', 'created_at' => $now->copy()->subMonths(24), 'updated_at' => $now->copy()->subDays(20)],
            ['brand_name' => 'Samsung', 'status' => 'active', 'created_at' => $now->copy()->subMonths(24), 'updated_at' => $now->copy()->subDays(20)],
            ['brand_name' => 'Sony', 'status' => 'active', 'created_at' => $now->copy()->subMonths(24), 'updated_at' => $now->copy()->subDays(20)],
            ['brand_name' => 'LG', 'status' => 'active', 'created_at' => $now->copy()->subMonths(24), 'updated_at' => $now->copy()->subDays(20)],
            ['brand_name' => 'HP', 'status' => 'active', 'created_at' => $now->copy()->subMonths(24), 'updated_at' => $now->copy()->subDays(20)],
            ['brand_name' => 'Dell', 'status' => 'active', 'created_at' => $now->copy()->subMonths(24), 'updated_at' => $now->copy()->subDays(20)],
        ];

        \App\Models\Brand::upsert(
            $brands,
            ['brand_name'],
            ['status', 'updated_at']
        );
    }
}
