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
            $table->unsignedBigInteger('user_id')->comment('Creador del permiso');
            $table->date('fecha')->comment('Fecha de inicio del permiso');
            $table->date('hasta')->comment('Fecha de finalización del perimiso');
            $table->boolean('dia_entero')->comment('Indica si el permiso cubre el día entero');
            $table->time('hora_inicio')->nullable()->comment('Hora de inicio del permiso');
            $table->time('hora_fin')->nullable()->comment('Hora de fin del permiso');
            $table->string('motivo')->nullable()->comment('Motivo del permiso');
            $table->enum('estado', ['pendiente', 'autorizado', 'rechazado'])
                  ->default('pendiente')
                  ->comment('Estado del permiso');
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permisos');
    }
}

