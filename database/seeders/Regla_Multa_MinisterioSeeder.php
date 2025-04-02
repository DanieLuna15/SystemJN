<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Regla_Multa_MinisterioSeeder extends Seeder
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

        ])->map(fn($asociacion) => [
            'ministerio_id' => $asociacion[1],
            'regla_multa_id' => $asociacion[1],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ])->toArray();

        DB::table('regla_multa_ministerio')->insert($asociaciones);
    }
}
