<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     */
    public function up(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            // Agregar la columna 'fecha' que puede tener valores nulos y se posicionará después de 'dia_semana'
            $table->date('fecha')->nullable()->after('dia_semana');   
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            // Eliminar la columna 'fecha'
            $table->dropColumn('fecha');
        });
    }
};

