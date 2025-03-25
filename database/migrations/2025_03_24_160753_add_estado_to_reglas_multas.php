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
        Schema::table('reglas_multas', function (Blueprint $table) {
            $table->boolean('estado')->default(true); // Agregar el campo 'estado' con valor predeterminado true
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reglas_multas', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
};
