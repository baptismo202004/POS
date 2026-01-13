<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['type_name' => 'Physical'],
            ['type_name' => 'Digital'],
            ['type_name' => 'Service'],
        ];

        foreach ($types as $type) {
            \App\Models\ProductType::create($type);
        }
    }
}
