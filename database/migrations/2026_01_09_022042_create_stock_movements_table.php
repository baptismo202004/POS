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
       Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnDelete();

            // Nullable because not all movements involve serials
            $table->foreignId('product_serial_id')
                ->nullable()
                ->constrained('product_serials')
                ->nullOnDelete();

            $table->enum('movement_type', [
                'stock_in',
                'sale',
                'return',
                'transfer',
                'waste',
                'adjustment'
            ]);

            // For non-serialized items (or bulk adjustments)
            $table->integer('quantity')->default(0);

            // Reference to sales, transfers, adjustments, etc.
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
