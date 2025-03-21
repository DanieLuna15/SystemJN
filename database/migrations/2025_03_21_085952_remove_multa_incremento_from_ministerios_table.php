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
        Schema::table('ministerios', function (Blueprint $table) {
            $table->dropColumn('multa_incremento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ministerios', function (Blueprint $table) {
            $table->decimal('multa_incremento', 8, 2)->default(2.00);
        });
    }
};
