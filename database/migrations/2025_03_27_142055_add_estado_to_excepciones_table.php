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
            $table->boolean('estado')
                  ->default(true)
                  ->comment('Indica si la excepción está activa o inactiva')
                  ->after('motivo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('excepciones', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
};
