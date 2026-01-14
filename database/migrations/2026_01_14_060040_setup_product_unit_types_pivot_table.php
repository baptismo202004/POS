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
        // 1. Create the pivot table
        Schema::create('product_unit_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_type_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // 2. Modify the products table
        Schema::table('products', function (Blueprint $table) {
            // Drop the old composite unique index first
            $table->dropUnique(['product_name', 'unit_type_id']);

            // Then drop the foreign key and the column
            $table->dropForeign(['unit_type_id']);
            $table->dropColumn('unit_type_id');

            // Finally, make the product name unique
            $table->unique('product_name');
        });
    }

    public function down(): void
    {
        // 1. Revert changes on products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['product_name']);
            $table->foreignId('unit_type_id')->nullable()->constrained();
            $table->unique(['product_name', 'unit_type_id']);
        });

        // 2. Drop the pivot table
        Schema::dropIfExists('product_unit_type');
    }
};
