<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Ministerio;
use Illuminate\Database\Seeder;

class MinisterioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ministerios = [
            ['nombre' => 'Líderes'],
            ['nombre' => 'Alabanza y Adoración'],
            ['nombre' => 'Panderos'],
            ['nombre' => 'Ujieres'],
            ['nombre' => 'Jóvenes'],
            ['nombre' => 'Niños'],
            ['nombre' => 'Ayuno'],
            ['nombre' => 'Oración e Intercesión'],
            ['nombre' => 'Tecnología y Medios de Comunicación'],
            ['nombre' => 'Evangelismo'],
            ['nombre' => 'Servicio y Voluntariado'],
            ['nombre' => 'Visitas'],
            ['nombre' => 'Teatro y Danza'],

        ];


        foreach ($ministerios as & $ministerio){
            $horario['created_at'] = Carbon::now();
            $horario['updated_at'] = Carbon::now();
        }

        Ministerio::insert($ministerios);
    }
}
