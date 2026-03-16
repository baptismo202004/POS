<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = now();

        $branches = [
            [
                'branch_name' => 'Main Branch',
                'address' => 'Poblacion, City Proper',
                'assign_to' => null,
                'status' => 'active',
                'created_at' => $now->copy()->subMonths(18),
                'updated_at' => $now->copy()->subDays(3),
            ],
            [
                'branch_name' => 'North Branch',
                'address' => 'North District, Highway Frontage',
                'assign_to' => null,
                'status' => 'active',
                'created_at' => $now->copy()->subMonths(14),
                'updated_at' => $now->copy()->subDays(6),
            ],
            [
                'branch_name' => 'South Branch',
                'address' => 'South District, Public Market Area',
                'assign_to' => null,
                'status' => 'active',
                'created_at' => $now->copy()->subMonths(12),
                'updated_at' => $now->copy()->subDays(2),
            ],
            [
                'branch_name' => 'East Branch',
                'address' => 'East District, Near Industrial Park',
                'assign_to' => null,
                'status' => 'active',
                'created_at' => $now->copy()->subMonths(10),
                'updated_at' => $now->copy()->subDays(9),
            ],
            [
                'branch_name' => 'West Branch',
                'address' => 'West District, Transport Terminal',
                'assign_to' => null,
                'status' => 'active',
                'created_at' => $now->copy()->subMonths(8),
                'updated_at' => $now->copy()->subDays(5),
            ],
        ];

        Branch::upsert(
            $branches,
            ['branch_name'],
            ['address', 'assign_to', 'status', 'updated_at']
        );
    }
}
