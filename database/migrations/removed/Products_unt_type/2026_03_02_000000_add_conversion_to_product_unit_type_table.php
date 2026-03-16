<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_unit_type', function (Blueprint $table) {
            if (!Schema::hasColumn('product_unit_type', 'conversion_factor')) {
                $table->decimal('conversion_factor', 10, 4)->default(1)->after('unit_type_id');
            }
            if (!Schema::hasColumn('product_unit_type', 'is_base')) {
                $table->boolean('is_base')->default(false)->after('conversion_factor');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_unit_type', function (Blueprint $table) {
            if (Schema::hasColumn('product_unit_type', 'is_base')) {
                $table->dropColumn('is_base');
            }
            if (Schema::hasColumn('product_unit_type', 'conversion_factor')) {
                $table->dropColumn('conversion_factor');
            }
        });
    }
};
