<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(UserTypeSeeder::class);

        User::create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'user_type_id' => 1, // Admin
        ]);
    }
}
