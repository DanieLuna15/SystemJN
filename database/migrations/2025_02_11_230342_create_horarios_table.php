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
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ministerio_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('dia_semana')->comment('1: Lunes, 2: Martes, ..., 7: Domingo');
            $table->time('hora_registro'); // Hora desde donde pueden marcar la huella
            $table->time('hora_multa'); // Hora desde donde corre la multa
            $table->boolean('estado')->default(1); // 1: Activo, 0: Inactivo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
