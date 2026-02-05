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
        // Check if there are any credits with missing customer_name that might be causing issues
        \DB::table('credits')
            ->whereNull('customer_name')
            ->update(['customer_name' => 'Updated Customer']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
