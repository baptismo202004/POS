<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('fulfilled_qty', 18, 2)->default(0)->after('quantity');
            $table->decimal('pending_qty', 18, 2)->default(0)->after('fulfilled_qty');
            $table->decimal('available_stock_at_sale', 18, 2)->default(0)->after('pending_qty');
            $table->enum('fulfillment_status', ['pending', 'partial', 'fulfilled'])->default('pending')->after('available_stock_at_sale');
            $table->boolean('is_for_procurement')->default(false)->after('fulfillment_status');
        });
    }

    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn([
                'fulfilled_qty',
                'pending_qty',
                'available_stock_at_sale',
                'fulfillment_status',
                'is_for_procurement',
            ]);
        });
    }
};
