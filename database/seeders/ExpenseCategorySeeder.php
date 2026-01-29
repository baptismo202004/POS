<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Purchases'],
            ['name' => 'Rent'],
            ['name' => 'Utilities'],
            ['name' => 'Salaries'],
            ['name' => 'Maintenance'],
            ['name' => 'Miscellaneous'],
        ];

        DB::table('expense_categories')->insertOrIgnore($categories);
    }
}
