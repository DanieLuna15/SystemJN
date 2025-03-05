<?php

namespace Database\Seeders;

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
            ['nombre' => 'Líderes', 'multa_incremento' => 2, 'tipo' => 1],
            ['nombre' => 'Alabanza y Adoración', 'multa_incremento' => 2, 'tipo' => 0],
            ['nombre' => 'Pandero', 'multa_incremento' => 2, 'tipo' => 0],
            ['nombre' => 'Ujieres', 'multa_incremento' => 2, 'tipo' => 0],
            ['nombre' => 'Jóvenes', 'multa_incremento' => 2, 'tipo' => 0],
            ['nombre' => 'Niños', 'multa_incremento' => 2, 'tipo' => 0],
            ['nombre' => 'Tecnología y Medios de Comunicación', 'multa_incremento' => 2, 'tipo' => 0],
            ['nombre' => 'Evangelismo', 'multa_incremento' => 2, 'tipo' => 0],
            ['nombre' => 'Servicio y Voluntariado', 'multa_incremento' => 2, 'tipo' => 0],
            ['nombre' => 'Ayuno', 'multa_incremento' => 2, 'tipo' => 0],
            ['nombre' => 'Oración e Intercesión', 'multa_incremento' => 2, 'tipo' => 0],
            ['nombre' => 'Visitas', 'multa_incremento' => 2, 'tipo' => 0],
            ['nombre' => 'Teatro y Danza', 'multa_incremento' => 2, 'tipo' => 0],

        ];

        Ministerio::insert($ministerios);
    }
}
