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
        $branches = [
            [
                'branch_name' => 'Main Branch',
                'address' => 'Lanas City of Naga, Cebu',
                'assign_to' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'branch_name' => 'RK',
                'address' => 'Lanas Elementary School',
                'assign_to' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'branch_name' => 'MCS',
                'address' => 'Lanas City of Naga, Cebu',
                'assign_to' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Branch::insert($branches);
    }
}
