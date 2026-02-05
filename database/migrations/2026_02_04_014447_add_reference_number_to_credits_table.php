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
            $table->string('reference_number')->unique()->after('id')->nullable();
        });
        
        // Generate reference numbers for existing records after column is added
        \DB::statement('UPDATE credits SET reference_number = CONCAT("CR-", YEAR(created_at), "-", LPAD(id, 4, "0")) WHERE reference_number IS NULL');
        
        // Make the column not nullable after filling existing records
        Schema::table('credits', function (Blueprint $table) {
            $table->string('reference_number')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credits', function (Blueprint $table) {
            $table->dropColumn('reference_number');
        });
    }
};
