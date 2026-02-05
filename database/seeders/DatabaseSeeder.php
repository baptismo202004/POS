<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserType;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductType;
use App\Models\UnitType;
use App\Models\RolePermission;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        UserType::truncate();
        Brand::truncate();
        Category::truncate();
        ProductType::truncate();
        UnitType::truncate();
        RolePermission::truncate();
        Branch::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->call([
            BranchSeeder::class,
            UserAndUserTypeSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            ProductTypeSeeder::class,
            UnitTypeSeeder::class,
            AdminFullAccessSeeder::class,
            ExpenseCategorySeeder::class,
            TestDataSeeder::class,
        ]);

    }
}
