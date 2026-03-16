<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_unit_type', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('unit_type_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('conversion_factor', 10, 4)->default(1);

            $table->boolean('is_base')->default(false);

            $table->unique(['product_id', 'unit_type_id']);
            $table->index(['product_id']);

            $table->timestamps();
        });

        // Enforce: only one base unit per product (MySQL 8+)
        // This uses a generated column that becomes the product_id only when is_base = 1.
        // Unique index on it ensures at most one base unit row per product.
        DB::statement("ALTER TABLE `product_unit_type` ADD COLUMN `base_product_id` BIGINT GENERATED ALWAYS AS (CASE WHEN `is_base` THEN `product_id` ELSE NULL END) STORED");
        DB::statement("CREATE UNIQUE INDEX `product_unit_type_one_base_per_product` ON `product_unit_type` (`base_product_id`)");
    }

    public function down(): void
    {
        // 2. Drop the pivot table if it exists
        Schema::dropIfExists('product_unit_type');
    }
};
