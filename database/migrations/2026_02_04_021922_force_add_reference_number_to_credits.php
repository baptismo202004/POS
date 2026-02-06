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
            if (!Schema::hasColumn('credits', 'reference_number')) {
                $table->string('reference_number')->unique()->after('id')->default('');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credits', function (Blueprint $table) {
            if (Schema::hasColumn('credits', 'reference_number')) {
                $table->dropColumn('reference_number');
            }
        });
    }
};
