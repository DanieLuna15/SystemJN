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
        Schema::create('regla_multa_ministerio', function (Blueprint $table) {
            $table->id(); // ID autoincremental
            $table->foreignId('ministerio_id')->constrained('ministerios')->onDelete('cascade'); // Relación con ministerios
            $table->foreignId('regla_multa_id')->constrained('reglas_multas')->onDelete('cascade'); // Relación con reglas_multas
            $table->timestamps(); // Campos created_at y updated_at

            $table->unique(['ministerio_id', 'regla_multa_id']); // Evita duplicados
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regla_multa_ministerio');
    }
};
