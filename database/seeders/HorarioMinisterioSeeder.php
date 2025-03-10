<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HorarioMinisterioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $asociaciones = collect([
            [1, 1],
            [1, 2],
            [1, 3],
            [1, 4],
            [2, 1],
            [2, 2],
            [2, 3],
            [2, 4],
            [3, 1],
            [3, 2],
            [3, 3],
            [3, 4],
            [4, 1],
            [4, 2],
            [4, 3],
            [4, 4],
            [5, 1],
            [6, 4],
            [7, 3],
            [8, 2],
            [9, 2],
            [10, 2]
        ])->map(fn($asociacion) => [
            'horario_id' => $asociacion[0],
            'ministerio_id' => $asociacion[1],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ])->toArray();

        DB::table('horario_ministerio')->insert($asociaciones);
    }
}
