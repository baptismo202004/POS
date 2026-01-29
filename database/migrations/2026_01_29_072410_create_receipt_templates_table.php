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
        Schema::create('receipt_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('sale'); // sale, refund, purchase, etc.
            $table->longText('header_content')->nullable(); // HTML/Blade template for header
            $table->longText('body_content')->nullable(); // HTML/Blade template for body
            $table->longText('footer_content')->nullable(); // HTML/Blade template for footer
            $table->longText('css_styles')->nullable(); // Custom CSS styles
            $table->json('settings')->nullable(); // Template settings as JSON
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('paper_size')->default('80mm'); // 80mm, A4, etc.
            $table->string('orientation')->default('portrait'); // portrait, landscape
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_templates');
    }
};
