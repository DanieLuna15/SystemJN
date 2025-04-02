<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReglasMultasIdToReglaMultaMinisterio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('regla_multa_ministerio', function (Blueprint $table) {
            // Asegúrate de que la columna no exista antes de agregarla
            if (!Schema::hasColumn('regla_multa_ministerio', 'reglas_multas_id')) {
                $table->unsignedBigInteger('reglas_multas_id')->nullable(); // Agregar la columna sin clave foránea
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('regla_multa_ministerio', function (Blueprint $table) {
            // Eliminar la columna si es necesario
            $table->dropColumn('reglas_multas_id');
        });
    }
}
