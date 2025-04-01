<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MinisterioUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Datos de ejemplo: usuario_id y ministerio_id
        $asociaciones = collect([
            [5, 2],
            [6, 1],
            [6, 2],
            [7, 1],
            [7, 2],
            [8, 1],
            [8, 2],
            [9, 1],
            [9, 3],
            [10, 3]
        ])->map(fn($asociacion) => [
            'user_id' => $asociacion[0], // Usuario
            'ministerio_id' => $asociacion[1], // Ministerio
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ])->toArray();

        // Limpiar la tabla antes de insertar nuevos datos
        DB::table('ministerio_user')->truncate();

        // Insertar los datos en la tabla
        DB::table('ministerio_user')->insert($asociaciones);
    }
}

