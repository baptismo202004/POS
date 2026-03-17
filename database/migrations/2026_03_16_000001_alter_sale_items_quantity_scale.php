<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE `sale_items` MODIFY `quantity` DECIMAL(18,2) NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `sale_items` MODIFY `quantity` DECIMAL(18,6) NOT NULL');
    }
};
