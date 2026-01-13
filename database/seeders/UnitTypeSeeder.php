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
        $units = [
            ['unit_name' => 'Piece (pc)'],
            ['unit_name' => 'Kilogram (kg)'],
            ['unit_name' => 'Gram (g)'],
            ['unit_name' => 'Liter (L)'],
            ['unit_name' => 'Milliliter (ml)'],
            ['unit_name' => 'Box'],
            ['unit_name' => 'Pack'],
        ];

        foreach ($units as $unit) {
            \App\Models\UnitType::create($unit);
        }
    }
}
