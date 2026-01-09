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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('barcode')->unique();

          $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
          $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
          $table->foreignId('product_type_id')->nullable()->constrained('product_types')->nullOnDelete();
          $table->foreignId('unit_type_id')->nullable()->constrained('unit_types')->nullOnDelete();

            $table->string('model_number')->nullable();
            $table->string('image')->nullable();

            $table->enum('tracking_type', ['none','serial','imei'])->default('none');

            $table->enum('warranty_type', ['none','shop','manufacturer'])->default('none');
            $table->integer('warranty_coverage_months')->nullable();

            $table->string('voltage_specs')->nullable();
            $table->enum('status', ['active','inactive'])->default('active');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
