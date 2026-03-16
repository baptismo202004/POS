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
        $now = now();

        $types = [
            ['type_name' => 'Physical', 'created_at' => $now->copy()->subMonths(24), 'updated_at' => $now->copy()->subDays(20)],
            ['type_name' => 'Digital', 'created_at' => $now->copy()->subMonths(24), 'updated_at' => $now->copy()->subDays(20)],
            ['type_name' => 'Service', 'created_at' => $now->copy()->subMonths(24), 'updated_at' => $now->copy()->subDays(20)],
        ];

        \App\Models\ProductType::upsert(
            $types,
            ['type_name'],
            ['updated_at']
        );
    }
}
