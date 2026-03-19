<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'category_type')) {
                $table->enum('category_type', [
                    'non_electronic',
                    'electronic_without_serial',
                    'electronic_with_serial',
                ])->default('non_electronic')->after('category_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'category_type')) {
                $table->dropColumn('category_type');
            }
        });
    }
};
