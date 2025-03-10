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
        Schema::table('horarios', function (Blueprint $table) {
            $table->dropForeign(['ministerio_id']); // Elimina la clave foránea
            $table->dropColumn('ministerio_id'); // Elimina la columna ministerio_id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            $table->foreignId('ministerio_id')->constrained('ministerios')->onDelete('cascade');
        });
    }
};
