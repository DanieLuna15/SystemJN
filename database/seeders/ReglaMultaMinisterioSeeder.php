<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReglaMultaMinisterioSeeder extends Seeder
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
            'regla_multa_id' => $asociacion[0],
            'ministerio_id' => $asociacion[1],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ])->toArray();

        DB::table('regla_multa_ministerio')->insert($asociaciones);
    }
}
