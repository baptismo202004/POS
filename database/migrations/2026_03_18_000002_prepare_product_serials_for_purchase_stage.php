<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // We need to support: serials entered during Purchase (no branch yet), then assigned during Stock-In.
        // Existing schema has: branch_id NOT NULL and status enum without 'purchased'.

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            // Make branch_id nullable (drop FK first), then re-add FK.
            DB::statement('ALTER TABLE product_serials DROP FOREIGN KEY product_serials_branch_id_foreign');
            DB::statement('ALTER TABLE product_serials MODIFY branch_id BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE product_serials ADD CONSTRAINT product_serials_branch_id_foreign FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE SET NULL');

            // Add purchase_id to link serials to purchase for traceability.
            if (!Schema::hasColumn('product_serials', 'purchase_id')) {
                Schema::table('product_serials', function (Blueprint $table) {
                    $table->foreignId('purchase_id')->nullable()->after('product_id')->constrained('purchases')->nullOnDelete();
                });
            }

            // Extend enum statuses to include 'purchased'
            DB::statement("ALTER TABLE product_serials MODIFY status ENUM('purchased','in_stock','sold','returned','defective','lost') NOT NULL DEFAULT 'purchased'");
        } else {
            // Fallback for other DBs (best effort).
            if (!Schema::hasColumn('product_serials', 'purchase_id')) {
                Schema::table('product_serials', function (Blueprint $table) {
                    $table->foreignId('purchase_id')->nullable()->after('product_id')->constrained('purchases')->nullOnDelete();
                });
            }

            Schema::table('product_serials', function (Blueprint $table) {
                if (Schema::hasColumn('product_serials', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable()->change();
                }
            });
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            // Revert enum (drop 'purchased') and restore default.
            DB::statement("ALTER TABLE product_serials MODIFY status ENUM('in_stock','sold','returned','defective','lost') NOT NULL DEFAULT 'in_stock'");

            // Remove purchase_id
            if (Schema::hasColumn('product_serials', 'purchase_id')) {
                Schema::table('product_serials', function (Blueprint $table) {
                    $table->dropConstrainedForeignId('purchase_id');
                });
            }

            // Restore branch_id NOT NULL and FK cascade
            DB::statement('ALTER TABLE product_serials DROP FOREIGN KEY product_serials_branch_id_foreign');
            DB::statement('ALTER TABLE product_serials MODIFY branch_id BIGINT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE product_serials ADD CONSTRAINT product_serials_branch_id_foreign FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE');
        } else {
            if (Schema::hasColumn('product_serials', 'purchase_id')) {
                Schema::table('product_serials', function (Blueprint $table) {
                    $table->dropConstrainedForeignId('purchase_id');
                });
            }
        }
    }
};
