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
        Schema::table('products', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['product_type_id']);
            
            // Modify the column to be a varchar instead of foreignId
            $table->dropColumn('product_type_id');
        });
        
        Schema::table('products', function (Blueprint $table) {
            // Add the column back as varchar
            $table->string('product_type_id')->nullable()->after('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('product_type_id');
        });
        
        Schema::table('products', function (Blueprint $table) {
            // Restore the original foreignId column
            $table->foreignId('product_type_id')->nullable()->constrained('product_types')->nullOnDelete();
        });
    }
};
