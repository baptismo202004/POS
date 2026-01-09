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
        Schema::create('product_serials', function (Blueprint $table) {
            $table->id(); // product_serials.id

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnDelete();

            $table->string('serial_number')->unique();

            $table->enum('status', [
                'in_stock',
                'sold',
                'returned',
                'defective',
                'lost'
            ])->default('in_stock');

            $table->date('warranty_expiry_date')->nullable();
            $table->timestamp('sold_at')->nullable();

            // Linked ONLY after a sale happens
            $table->foreignId('sale_item_id')
                ->nullable()
                ->constrained('sale_items')
                ->nullOnDelete();

            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_serials');
    }
};
