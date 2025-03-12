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
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_name')->after('name');
            $table->string('address')->nullable()->after('last_name');
            $table->string('ci')->unique()->after('address');
            $table->string('profile_image')->nullable()->after('ci');
            $table->string('phone')->nullable()->after('ci'); 
            $table->boolean('estado')->default(true)->after('phone'); 
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_name', 'address', 'ci', 'profile_image', 'phone', 'estado']);
        });
    }
};
