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
        Schema::create('excepciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id')->comment('Usuario que creó la excepción'); // Relación con el usuario creador
            $table->date('fecha')->comment('Fecha de inicio de la excepción'); // Fecha de inicio
            $table->date('hasta')->comment('Fecha de finalización de la excepción'); // Fecha final
            $table->boolean('dia_entero')->default(1)->comment('Indica si la excepción cubre el día entero (1 = Sí, 0 = No, 2 = VARIOS DIAS)'); // Día completo
            $table->time('hora_inicio')->nullable()->comment('Hora de inicio de la excepción (si no es día completo)'); // Hora de inicio
            $table->time('hora_fin')->nullable()->comment('Hora de finalización de la excepción (si no es día completo)'); // Hora de fin
            $table->string('motivo')->nullable()->comment('Motivo de la excepción'); // Motivo o razón de la excepción
            $table->timestamps();

            // Clave foránea para el usuario creador
            //$table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('excepciones');
    }
};
