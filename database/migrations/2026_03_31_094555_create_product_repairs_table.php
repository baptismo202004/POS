<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_repairs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('product_serial_id')->nullable()->constrained('product_serials')->nullOnDelete();
            $table->foreignId('sale_item_id')->nullable()->constrained('sale_items')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('serial_number')->nullable();
            $table->enum('repair_type', ['in_warranty', 'out_of_warranty', 'inspection'])->default('in_warranty');
            $table->enum('status', ['received', 'in_progress', 'repaired', 'returned', 'unrepairable'])->default('received');

            $table->text('issue_description');
            $table->text('resolution_notes')->nullable();
            $table->decimal('repair_cost', 10, 2)->default(0);

            $table->date('received_date');
            $table->date('returned_date')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_repairs');
    }
};
