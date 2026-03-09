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
        Schema::table('sale_items', function (Blueprint $table) {
            $table->foreignId('unit_type_id')->nullable()->after('product_id')->constrained('unit_types')->nullOnDelete();
            $table->index(['product_id', 'unit_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropForeign(['unit_type_id']);
            $table->dropIndex(['product_id', 'unit_type_id']);
            $table->dropColumn('unit_type_id');
        });
    }
};
