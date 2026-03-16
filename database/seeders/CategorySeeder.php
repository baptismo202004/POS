<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $categories = [
            ['category_name' => 'Electronics', 'status' => 'active', 'created_at' => $now->copy()->subMonths(24), 'updated_at' => $now->copy()->subDays(20)],
            ['category_name' => 'Computers', 'status' => 'active', 'created_at' => $now->copy()->subMonths(24), 'updated_at' => $now->copy()->subDays(20)],
            ['category_name' => 'Appliances', 'status' => 'active', 'created_at' => $now->copy()->subMonths(24), 'updated_at' => $now->copy()->subDays(20)],
            ['category_name' => 'Groceries', 'status' => 'active', 'created_at' => $now->copy()->subMonths(24), 'updated_at' => $now->copy()->subDays(20)],
            ['category_name' => 'Fashion', 'status' => 'active', 'created_at' => $now->copy()->subMonths(24), 'updated_at' => $now->copy()->subDays(20)],
        ];

        \App\Models\Category::upsert(
            $categories,
            ['category_name'],
            ['status', 'updated_at']
        );
    }
}
