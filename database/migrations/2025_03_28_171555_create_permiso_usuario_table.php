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
        Schema::create('permiso_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade')->comment('Relación con la tabla users');
                $table->foreignId('permiso_id')->constrained('permisos')->onDelete('cascade')->comment('Relación con la tabla permisos');
                $table->timestamps();
                $table->unique(['usuario_id', 'permiso_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permiso_usuario');
    }
};
