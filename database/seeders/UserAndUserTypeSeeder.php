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
        DB::table('user_types')->insert([
            [
                'id' => 1,
                'name' => 'Admin',
                'description' => 'System Administrator',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Cashier',
                'description' => 'POS Cashier',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Manager',
                'description' => 'Store Manager',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ============================
        // INSERT USERS
        // ============================
        DB::table('users')->insert([
            [
                'name' => 'Admin User',
                'email' => 'dablo@sample.com',
                'password' => Hash::make('password'),
                'profile_picture' => null,
                'user_type_id' => 1, // Admin
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cashier User',
                'email' => 'cashier@example.com',
                'password' => Hash::make('password'),
                'profile_picture' => null,
                'user_type_id' => 2, // Cashier
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
