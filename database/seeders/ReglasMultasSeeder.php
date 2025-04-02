<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReglasMultasSeeder extends Seeder
{
    public function run()
    {
        $reglas_multas = collect([
            [
                'multa_por_falta' => 40,
                'minutos_por_incremento' => 5,
                'multa_incremental' => 2,
                'minutos_retraso_largo' => 30,
                'multa_por_retraso_largo' => 20,
            ],
        ])->map(function ($regla) {
            return array_merge($regla, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        })->toArray();

        DB::table('reglas_multas')->insert($reglas_multas);
    }
}
