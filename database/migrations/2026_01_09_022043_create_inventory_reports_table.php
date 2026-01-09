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
        Schema::create('inventory_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('branch_id')->constrained('branches');

            $table->date('date_from');
            $table->date('date_to');

            $table->json('report_data');
            $table->timestamp('generated_at')->useCurrent();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_reports');
    }
};
