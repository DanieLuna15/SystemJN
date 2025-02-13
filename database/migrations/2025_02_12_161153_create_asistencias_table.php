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
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('miembro_id');
            $table->date('fecha');
            $table->time('hora_ingreso');
            $table->boolean('tarde')->default(0);
            $table->decimal('multa', 8, 2)->default(0);
            $table->timestamps();
    
            // $table->foreign('miembro_id')->references('id')->on('miembros')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
