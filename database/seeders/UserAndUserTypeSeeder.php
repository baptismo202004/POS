<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserAndUserTypeSeeder extends Seeder
{
    public function run(): void
    {
        // ============================
        // INSERT USER TYPES FIRST
        // ============================
        $now = now();

        DB::table('user_types')->upsert(
            [
                [
                    'id' => 1,
                    'name' => 'Admin',
                    'description' => 'System Administrator',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id' => 2,
                    'name' => 'Cashier',
                    'description' => 'POS Cashier',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id' => 3,
                    'name' => 'Manager',
                    'description' => 'Store Manager',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            ['id'],
            ['name', 'description', 'updated_at']
        );

        // ============================
        // INSERT USERS
        // ============================
        DB::table('users')->upsert(
            [
                [
                    'employee_id' => 'EMP00001',
                    'name' => 'Admin User',
                    'email' => 'dablo@sample.com',
                    'password' => Hash::make('password'),
                    'profile_picture' => null,
                    'user_type_id' => 1,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'employee_id' => 'EMP00002',
                    'name' => 'Cashier User',
                    'email' => 'cashier@example.com',
                    'password' => Hash::make('password'),
                    'profile_picture' => null,
                    'user_type_id' => 2,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            ['email'],
            ['employee_id', 'name', 'password', 'profile_picture', 'user_type_id', 'status', 'updated_at']
        );
    }
}
