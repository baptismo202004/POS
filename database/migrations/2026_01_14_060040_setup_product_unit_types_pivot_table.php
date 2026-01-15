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
        // 1. Create the pivot table if it doesn't exist
        if (!Schema::hasTable('product_unit_type')) {
            Schema::create('product_unit_type', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->foreignId('unit_type_id')->constrained()->onDelete('cascade');
                $table->timestamps();
            });
        }

        // 2. Modify the products table
        Schema::table('products', function (Blueprint $table) {
            // Check if the column exists before trying to drop it
            if (Schema::hasColumn('products', 'unit_type_id')) {
                // To drop the foreign key, we need its name. Laravel's default is products_unit_type_id_foreign
                $table->dropForeign(['unit_type_id']);
                $table->dropColumn('unit_type_id');
            }
            // Drop composite unique index if it exists
            // Note: Manually check index name in your DB schema if this fails.
            // Default name: products_product_name_unit_type_id_unique
            // Since the column is dropped, the index should be gone, but we check to be safe.

            // Finally, make the product name unique if it isn't already
            $table->unique('product_name');
        });
    }

    public function down(): void
    {
        // 1. Revert changes on products table
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'product_name')) {
                $table->dropUnique(['product_name']);
            }
            if (!Schema::hasColumn('products', 'unit_type_id')) {
                $table->foreignId('unit_type_id')->nullable()->constrained();
                $table->unique(['product_name', 'unit_type_id']);
            }
        });

        // 2. Drop the pivot table if it exists
        Schema::dropIfExists('product_unit_type');
    }
};
