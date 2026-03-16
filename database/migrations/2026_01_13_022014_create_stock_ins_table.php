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
        Schema::create('stock_ins', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('branch_id')
                ->constrained()
                ->onDelete('cascade');

            // From migration 3
            $table->foreignId('unit_type_id')
                ->nullable()
                ->constrained();

            // From migration 2
            $table->foreignId('purchase_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');

            $table->integer('quantity');

            // From migration 4 (price nullable)
            $table->decimal('price', 8, 2)
                ->nullable();

            $table->integer('sold')
                ->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_ins');
    }
};