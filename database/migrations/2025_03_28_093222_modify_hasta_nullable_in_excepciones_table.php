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
        Schema::table('excepciones', function (Blueprint $table) {
            $table->date('hasta')->nullable()->comment('Fecha de finalización de la excepción')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('excepciones', function (Blueprint $table) {
            $table->date('hasta')->comment('Fecha de finalización de la excepción')->change();
        });
    }
};
