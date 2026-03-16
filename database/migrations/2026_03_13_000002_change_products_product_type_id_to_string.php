<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'product_type_id')) {
                try {
                    $table->dropForeign(['product_type_id']);
                } catch (\Throwable $e) {
                    // ignore if foreign key doesn't exist
                }

                $table->dropColumn('product_type_id');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'product_type_id')) {
                $table->string('product_type_id')->nullable()->after('category_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'product_type_id')) {
                $table->dropColumn('product_type_id');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'product_type_id')) {
                $table->foreignId('product_type_id')->nullable()->constrained('product_types')->nullOnDelete();
            }
        });
    }
};
