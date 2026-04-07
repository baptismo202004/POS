<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warranty_records', function (Blueprint $table) {
            $table->id();

            // What product is covered
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();

            // For serial-tracked items — links to the specific unit
            $table->foreignId('product_serial_id')
                ->nullable()
                ->constrained('product_serials')
                ->nullOnDelete();

            // Where the warranty originated
            $table->foreignId('purchase_id')
                ->nullable()
                ->constrained('purchases')
                ->nullOnDelete();

            $table->foreignId('purchase_item_id')
                ->nullable()
                ->constrained('purchase_items')
                ->nullOnDelete();

            // Where the warranty was activated (at point of sale)
            $table->foreignId('sale_id')
                ->nullable()
                ->constrained('sales')
                ->nullOnDelete();

            $table->foreignId('sale_item_id')
                ->nullable()
                ->constrained('sale_items')
                ->nullOnDelete();

            // Customer who holds the warranty
            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('customers')
                ->nullOnDelete();

            // Branch that issued the warranty
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            // Warranty details
            $table->enum('warranty_type', ['shop', 'manufacturer'])->default('shop');

            $table->integer('coverage_months')->unsigned()->default(0);

            $table->date('start_date')->nullable();   // when warranty begins (sale date or purchase date)
            $table->date('expiry_date')->nullable();  // start_date + coverage_months

            $table->enum('status', ['active', 'expired', 'voided', 'claimed'])->default('active');

            // For non-serial items: how many units this record covers
            $table->decimal('quantity', 18, 2)->default(1);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['product_id', 'status']);
            $table->index(['sale_id']);
            $table->index(['customer_id']);
            $table->index(['expiry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warranty_records');
    }
};
