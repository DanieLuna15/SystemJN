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
        Schema::create('importaciones_asistencia', function (Blueprint $table) {
            $table->id();
            $table->string('archivo'); // Nombre del archivo
            $table->string('ruta'); // Ruta en el servidor
            $table->unsignedBigInteger('usuario_id'); // Usuario que subió el archivo
            $table->enum('estado', ['pendiente', 'procesado', 'fallido'])->default('pendiente');
            $table->text('errores')->nullable(); // Para almacenar errores en la importación
            $table->timestamps();
    
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('importaciones_asistencia');
    }
};
