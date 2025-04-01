<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MinisterioLiderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $asociaciones = collect([
            [7, 2],
            [8, 2],
            [9, 3],
        ])->map(fn($asociacion) => [
            'user_id' => $asociacion[0], // ID del líder
            'ministerio_id' => $asociacion[1], // ID del ministerio
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ])->toArray();

        // Limpiar la tabla antes de insertar nuevos datos
        DB::table('ministerio_lider')->truncate();

        // Insertar los datos en la tabla
        DB::table('ministerio_lider')->insert($asociaciones);
    }
}

