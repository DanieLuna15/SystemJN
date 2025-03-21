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
            ['nombre' => 'Líderes', 'tipo' => 1],
            ['nombre' => 'Alabanza y Adoración', 'tipo' => 0],
            ['nombre' => 'Panderos', 'tipo' => 0],
            ['nombre' => 'Ujieres', 'tipo' => 0],
            ['nombre' => 'Jóvenes', 'tipo' => 0],
            ['nombre' => 'Niños', 'tipo' => 0],
            ['nombre' => 'Ayuno', 'tipo' => 0],
            ['nombre' => 'Oración e Intercesión', 'tipo' => 0],
            ['nombre' => 'Tecnología y Medios de Comunicación', 'tipo' => 0],
            ['nombre' => 'Evangelismo', 'tipo' => 0],
            ['nombre' => 'Servicio y Voluntariado', 'tipo' => 0],
            ['nombre' => 'Visitas', 'tipo' => 0],
            ['nombre' => 'Teatro y Danza', 'tipo' => 0],
        ];
        

        foreach ($ministerios as & $ministerio){
            $horario['created_at'] = Carbon::now();
            $horario['updated_at'] = Carbon::now();
        }

        Ministerio::insert($ministerios);
    }
}
