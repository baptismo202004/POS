<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_type_id')
                  ->constrained('user_types')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
            $table->string('module'); 
            $table->string('ability');
            $table->timestamps();
            $table->unique(['user_type_id', 'module', 'ability']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
