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
use Database\Seeders\Pos\BaseDataSeeder;
use Database\Seeders\Pos\PurchaseSeeder;
use Database\Seeders\Pos\InventorySeeder;
use Database\Seeders\Pos\SalesSeeder;
use Database\Seeders\Pos\CreditSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('credit_payments')->truncate();
        DB::table('credits')->truncate();
        DB::table('sale_items')->truncate();
        DB::table('stock_outs')->truncate();
        DB::table('sales')->truncate();
        DB::table('stock_movements')->truncate();
        DB::table('stock_in_unit_prices')->truncate();
        DB::table('stock_transfers')->truncate();
        DB::table('stock_ins')->truncate();
        DB::table('purchase_items')->truncate();
        DB::table('purchases')->truncate();
        DB::table('expenses')->truncate();
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
            BaseDataSeeder::class,
            PurchaseSeeder::class,
            InventorySeeder::class,
            SalesSeeder::class,
            CreditSeeder::class,
        ]);

    }
}
