<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ministerios', function (Blueprint $table) {
            if (Schema::hasColumn('ministerios', 'hora_tolerancia')) {
                $table->dropColumn('hora_tolerancia'); 
            }
            $table->boolean('estado')->default(true)->after('multa_incremento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ministerios', function (Blueprint $table) {
            $table->time('hora_tolerancia')->nullable(); 
            $table->dropColumn('estado'); 
        });
    }
};
