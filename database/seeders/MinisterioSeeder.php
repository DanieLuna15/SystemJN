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
            ['nombre' => 'Ministerio de Adoración', 'logo' => null, 'multa_incremento' => 2, 'tipo' => 0, 'estado' => 1],
            ['nombre' => 'Ministerio de Niños', 'logo' => null, 'multa_incremento' => 2, 'tipo' => 0, 'estado' => 1],
            ['nombre' => 'Ministerio de Jóvenes', 'logo' => null, 'multa_incremento' => 2, 'tipo' => 0, 'estado' => 1],
            ['nombre' => 'Ministerio de Líderes', 'logo' => null, 'multa_incremento' => 2, 'tipo' => 1, 'estado' => 1],
            ['nombre' => 'Ministerio de Evangelismo', 'logo' => null, 'multa_incremento' => 2, 'tipo' => 0, 'estado' => 1],
            ['nombre' => 'Ministerio de Servicio y Voluntariado', 'logo' => null, 'multa_incremento' => 2, 'tipo' => 0, 'estado' => 1],
            ['nombre' => 'Ministerio de Ayuno', 'logo' => null, 'multa_incremento' => 2, 'tipo' => 0, 'estado' => 1],
            ['nombre' => 'Ministerio de Oración e Intercesión', 'logo' => null, 'multa_incremento' => 2, 'tipo' => 0, 'estado' => 1],
            ['nombre' => 'Ministerio de Alabanza y Adoración', 'logo' => null, 'multa_incremento' => 2, 'tipo' => 0, 'estado' => 1],
            ['nombre' => 'Ministerio de Visitas', 'logo' => null, 'multa_incremento' => 2, 'tipo' => 0, 'estado' => 1],
            ['nombre' => 'Ministerio de Ujieres', 'logo' => null, 'multa_incremento' => 2, 'tipo' => 0, 'estado' => 1],
            ['nombre' => 'Ministerio de Teatro y Danza', 'logo' => null, 'multa_incremento' => 2, 'tipo' => 0, 'estado' => 1],
            ['nombre' => 'Ministerio de Pandero', 'logo' => null, 'multa_incremento' => 2, 'tipo' => 0, 'estado' => 1],
            ['nombre' => 'Ministerio de Tecnología y Medios de Comunicación', 'logo' => null, 'multa_incremento' => 2, 'tipo' => 0, 'estado' => 1],
        ];

        // Insertar en la base de datos
        Ministerio::insert($ministerios);
    }
}
