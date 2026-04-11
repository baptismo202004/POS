<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'selling_price')) {
                $table->decimal('selling_price', 10, 2)->nullable()->after('status');
            }
            if (! Schema::hasColumn('products', 'purchase_price')) {
                $table->decimal('purchase_price', 10, 2)->nullable()->after('selling_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('products', 'selling_price')) {
                $cols[] = 'selling_price';
            }
            if (Schema::hasColumn('products', 'purchase_price')) {
                $cols[] = 'purchase_price';
            }
            if (! empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
