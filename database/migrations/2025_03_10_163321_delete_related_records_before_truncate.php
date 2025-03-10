<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Eliminar la clave foránea
        Schema::table('horario_ministerio', function ($table) {
            $table->dropForeign(['horario_id']);
        });

        // Ahora puedes truncar la tabla
        DB::statement('TRUNCATE TABLE horarios');

        // Reagregar la clave foránea si es necesario
        Schema::table('horario_ministerio', function ($table) {
            $table->foreign('horario_id')->references('id')->on('horarios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar las claves foráneas antes de truncar las tablas
        Schema::table('horario_ministerio', function (Blueprint $table) {
            $table->dropForeign(['horario_id']); // Aquí 'horario_id' es el nombre de la columna de la clave foránea
        });

        // Eliminar los registros relacionados antes de truncar la tabla
        DB::table('horario_ministerio')->whereNotNull('horario_id')->delete();

        // Ahora puedes truncar la tabla
        DB::statement('TRUNCATE TABLE horarios');

        // Opcionalmente, si deseas restaurar las claves foráneas después, lo haces aquí
        Schema::table('horario_ministerio', function (Blueprint $table) {
            $table->foreign('horario_id')->references('id')->on('horarios')->onDelete('cascade');
        });
    }
};
