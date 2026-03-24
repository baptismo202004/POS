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
        Schema::create('stock_ins_head', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('purchase_id')->nullable()->constrained('purchases')->onDelete('set null');
            $table->date('stock_in_date');
            $table->string('reference_number')->nullable()->unique();
            $table->text('notes')->nullable();
            $table->decimal('total_quantity', 18, 2)->default(0);
            $table->string('status', 20)->default('completed'); // completed, pending, cancelled
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index('branch_id');
            $table->index('purchase_id');
            $table->index('stock_in_date');
            $table->index('status');
            $table->index(['branch_id', 'stock_in_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_ins_head');
    }
};
