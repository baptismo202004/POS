<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
<<<<<<< HEAD
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
=======
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('selling_price', 10, 2)->nullable()->after('status');
>>>>>>> ad4ac38 (Cashier and Admin update)
            $table->decimal('purchase_price', 10, 2)->nullable()->after('selling_price');
        });
    }

<<<<<<< HEAD
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('purchase_price');
=======
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['selling_price', 'purchase_price']);
>>>>>>> ad4ac38 (Cashier and Admin update)
        });
    }
};
