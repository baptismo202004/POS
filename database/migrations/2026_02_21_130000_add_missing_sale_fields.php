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
        Schema::table('sales', function (Blueprint $table) {
            // Add status column if it doesn't exist
            if (!Schema::hasColumn('sales', 'status')) {
                $table->enum('status', ['completed', 'voided', 'pending'])->default('completed')->after('payment_method');
            }
            
            // Add voided_by column if it doesn't exist
            if (!Schema::hasColumn('sales', 'voided_by')) {
                $table->foreignId('voided_by')->nullable()->after('voided')->constrained('users')->nullOnDelete();
            }
            
            // Add voided_at column if it doesn't exist
            if (!Schema::hasColumn('sales', 'voided_at')) {
                $table->timestamp('voided_at')->nullable()->after('voided_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['status', 'voided_by', 'voided_at']);
        });
    }
};
