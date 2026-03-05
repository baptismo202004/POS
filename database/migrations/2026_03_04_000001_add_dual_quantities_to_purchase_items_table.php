<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->decimal('primary_quantity', 16, 4)->nullable()->after('product_id');
            $table->decimal('multiplier', 16, 4)->nullable()->after('primary_quantity');
            $table->decimal('base_quantity', 16, 4)->nullable()->after('multiplier');
            $table->foreignId('base_unit_type_id')->nullable()->constrained('unit_types')->after('unit_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropForeign(['base_unit_type_id']);
            $table->dropColumn('base_unit_type_id');
            $table->dropColumn('base_quantity');
            $table->dropColumn('multiplier');
            $table->dropColumn('primary_quantity');
        });
    }
};
