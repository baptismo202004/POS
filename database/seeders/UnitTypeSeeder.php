<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $units = [
            ['unit_name' => 'Piece (pc)', 'created_at' => $now->copy()->subMonths(18), 'updated_at' => $now->copy()->subDays(10)],
            ['unit_name' => 'Gram (g)', 'created_at' => $now->copy()->subMonths(18), 'updated_at' => $now->copy()->subDays(10)],
            ['unit_name' => 'Kilogram (kg)', 'created_at' => $now->copy()->subMonths(18), 'updated_at' => $now->copy()->subDays(10)],
            ['unit_name' => 'Liter (L)', 'created_at' => $now->copy()->subMonths(18), 'updated_at' => $now->copy()->subDays(10)],
            ['unit_name' => 'Milliliter (ml)', 'created_at' => $now->copy()->subMonths(18), 'updated_at' => $now->copy()->subDays(10)],
            ['unit_name' => 'Box', 'created_at' => $now->copy()->subMonths(18), 'updated_at' => $now->copy()->subDays(10)],
            ['unit_name' => 'Pack', 'created_at' => $now->copy()->subMonths(18), 'updated_at' => $now->copy()->subDays(10)],
            ['unit_name' => 'Bottle', 'created_at' => $now->copy()->subMonths(18), 'updated_at' => $now->copy()->subDays(10)],
            ['unit_name' => 'Sachet', 'created_at' => $now->copy()->subMonths(18), 'updated_at' => $now->copy()->subDays(10)],
            ['unit_name' => 'Can', 'created_at' => $now->copy()->subMonths(18), 'updated_at' => $now->copy()->subDays(10)],
            ['unit_name' => 'Jar', 'created_at' => $now->copy()->subMonths(18), 'updated_at' => $now->copy()->subDays(10)],
            ['unit_name' => 'Roll', 'created_at' => $now->copy()->subMonths(18), 'updated_at' => $now->copy()->subDays(10)],
            ['unit_name' => 'Case', 'created_at' => $now->copy()->subMonths(18), 'updated_at' => $now->copy()->subDays(10)],
        ];

        \App\Models\UnitType::upsert(
            $units,
            ['unit_name'],
            ['updated_at']
        );
    }
}
