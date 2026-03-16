<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_movements', 'source_type')) {
                $table->string('source_type')->nullable()->after('movement_type');
            }

            if (!Schema::hasColumn('stock_movements', 'source_id')) {
                $table->unsignedBigInteger('source_id')->nullable()->after('source_type');
            }

            if (!Schema::hasColumn('stock_movements', 'quantity_base')) {
                $table->decimal('quantity_base', 18, 6)->default(0)->after('source_id');
            }
        });

        // Ensure movement_type supports the values used by the refactored inventory flow.
        // We use raw SQL to avoid requiring doctrine/dbal for enum modifications.
        DB::statement("ALTER TABLE `stock_movements` MODIFY COLUMN `movement_type` ENUM('stock_in','purchase','sale','return','transfer','waste','adjustment') NOT NULL");

        Schema::table('stock_movements', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_movements', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }

            if (!Schema::hasColumn('stock_movements', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }

            $table->index(['source_type', 'source_id']);
            $table->index(['product_id', 'branch_id']);
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            if (Schema::hasColumn('stock_movements', 'quantity_base')) {
                $table->dropColumn('quantity_base');
            }

            if (Schema::hasColumn('stock_movements', 'source_id')) {
                $table->dropColumn('source_id');
            }

            if (Schema::hasColumn('stock_movements', 'source_type')) {
                $table->dropColumn('source_type');
            }

            $table->dropIndex(['source_type', 'source_id']);
            $table->dropIndex(['product_id', 'branch_id']);
        });

        DB::statement("ALTER TABLE `stock_movements` MODIFY COLUMN `movement_type` ENUM('stock_in','sale','return','transfer','waste','adjustment') NOT NULL");
    }
};
