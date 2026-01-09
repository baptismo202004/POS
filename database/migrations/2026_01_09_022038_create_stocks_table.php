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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('branch_id')->constrained('branches');

            $table->integer('quantity_on_hand')->default(0);
            $table->integer('reorder_level')->default(0);
            $table->decimal('average_cost', 12, 2)->default(0);

            $table->timestamp('last_stock_in_at')->nullable();
            $table->timestamp('last_stock_out_at')->nullable();

            $table->timestamps();

            $table->unique(['product_id', 'branch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
