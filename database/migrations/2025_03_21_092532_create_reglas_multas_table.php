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
        Schema::create('reglas_multas', function (Blueprint $table) {
            $table->id(); // ID autoincremental
            $table->string('nombre')->nullable(); // Nombre de la regla (puede ser nulo)
            $table->decimal('multa_por_falta', 8, 2)->default(40.00); // Multa por falta (40 bs)
            $table->integer('minutos_por_incremento')->default(5); // Minutos por incremento (5 minutos)
            $table->decimal('multa_incremental', 8, 2)->default(2.00); // Multa incremental (2 bs)
            $table->integer('minutos_retraso_largo')->default(30); // Minutos de retraso largo (30 minutos)
            $table->decimal('multa_por_retraso_largo', 8, 2)->default(20.00); // Multa por retraso largo (20 bs)
            $table->timestamps(); // Campos created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reglas_multas');
    }
};
