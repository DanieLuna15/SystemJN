<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermisosTable extends Migration

{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permisos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('Creador de la excepción');
            $table->date('fecha')->comment('Fecha de inicio de la excepción');
            $table->date('hasta')->comment('Fecha de finalización de la excepción');
            $table->boolean('dia_entero')->comment('Indica si la excepción cubre el día entero');
            $table->time('hora_inicio')->nullable()->comment('Hora de inicio de la excepción');
            $table->time('hora_fin')->nullable()->comment('Hora de fin de la excepción');
            $table->string('motivo')->nullable()->comment('Motivo de la excepción');
            $table->enum('estado', ['pendiente', 'autorizado', 'rechazado'])
                  ->default('pendiente')
                  ->comment('Estado de la excepción');
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permisos');
    }
}

