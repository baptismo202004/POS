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
        Schema::table('products', function (Blueprint $table) {
            // Add stock management fields
            if (!Schema::hasColumn('products', 'min_stock_level')) {
                $table->integer('min_stock_level')->default(5)->after('status');
            }
            
            if (!Schema::hasColumn('products', 'max_stock_level')) {
                $table->integer('max_stock_level')->default(100)->after('min_stock_level');
            }
            
            if (!Schema::hasColumn('products', 'stock_status')) {
                $table->string('stock_status')->default('in_stock')->after('max_stock_level');
            }
            
            // Add indexes for better performance
            if (!Schema::hasIndex('products', ['min_stock_level', 'max_stock_level'])) {
                $table->index(['min_stock_level', 'max_stock_level']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'min_stock_level')) {
                $table->dropColumn('min_stock_level');
            }
            
            if (Schema::hasColumn('products', 'max_stock_level')) {
                $table->dropColumn('max_stock_level');
            }
            
            if (Schema::hasColumn('products', 'stock_status')) {
                $table->dropColumn('stock_status');
            }
        });
    }
};
