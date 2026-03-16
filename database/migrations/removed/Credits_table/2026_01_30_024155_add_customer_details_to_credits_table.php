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
        Schema::table('credits', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('customer_name');
            $table->string('email')->nullable()->after('phone');
            $table->text('address')->nullable()->after('email');
        });
    }   
    public function down(): void
    {
        Schema::table('credits', function (Blueprint $table) {
            $table->dropColumn(['phone', 'email', 'address']);
        });
    }
};
