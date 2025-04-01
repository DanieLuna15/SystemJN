<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('permisos', function (Blueprint $table) {
            $table->date('hasta')->nullable()->change(); // Permitir valores NULL en 'hasta'
            $table->boolean('dia_entero')->default(1)->change(); // Valor por defecto 1 en 'Todo el dia' 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permisos', function (Blueprint $table) {
            $table->date('hasta')->nullable(false)->change(); // Revertir a 'not null'
            $table->boolean('dia_entero')->default(null)->change(); // Revertir el valor por defecto
        });
    }
};
