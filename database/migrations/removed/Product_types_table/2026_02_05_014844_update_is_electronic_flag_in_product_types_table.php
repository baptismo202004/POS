<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Set the is_electronic flag based on the product type name to ensure correct product handling
        DB::table('product_types')->where('type_name', 'Electronic')->update(['is_electronic' => true]);
        DB::table('product_types')->where('type_name', 'Non-Electronic')->update(['is_electronic' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the changes, setting all to a default of false
        DB::table('product_types')->update(['is_electronic' => false]);
    }
};
