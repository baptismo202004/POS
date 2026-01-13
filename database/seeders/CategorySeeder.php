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
        $categories = [
            ['category_name' => 'Electronics', 'status' => 'active'],
            ['category_name' => 'Computers', 'status' => 'active'],
            ['category_name' => 'Appliances', 'status' => 'active'],
            ['category_name' => 'Groceries', 'status' => 'active'],
            ['category_name' => 'Fashion', 'status' => 'active'],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
