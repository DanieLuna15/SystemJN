<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ministerios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->decimal('multa_incremento', 8, 2)->default(2.00); // Monto de multa cada 5 min
            $table->time('hora_tolerancia')->nullable(); // Nueva forma de tolerancia
            $table->string('logo')->nullable(); // Imagen referencial
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ministerios');
    }
};
