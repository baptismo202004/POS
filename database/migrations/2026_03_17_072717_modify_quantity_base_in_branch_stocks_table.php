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
    Schema::table('branch_stocks', function (Blueprint $table) {
        $table->decimal('quantity_base', 18, 0)->default(0)->change();
    });
}

public function down(): void
{
    Schema::table('branch_stocks', function (Blueprint $table) {
        $table->decimal('quantity_base', 18, 6)->default(0)->change();
    });
}
};
