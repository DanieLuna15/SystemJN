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
        Schema::create('excepcion_ministerio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ministerio_id')->constrained('ministerios')->onDelete('cascade')->comment('Relación con la tabla ministerios');
            $table->foreignId('excepcion_id')->constrained('excepciones')->onDelete('cascade')->comment('Relación con la tabla excepciones'); 
            $table->timestamps();
            $table->unique(['ministerio_id', 'excepcion_id'], 'unique_ministerio_excepcion'); // Garantizar combinaciones únicas
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('excepcion_ministerio');
    }
};
