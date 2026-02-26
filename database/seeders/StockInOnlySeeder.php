<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StockInOnlySeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            StockInSeeder::class,
        ]);
    }
}
