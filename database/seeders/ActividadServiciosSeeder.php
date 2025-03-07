<?php

namespace Database\Seeders;

use App\Models\ActividadServicio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActividadServiciosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $servicios = [
            ['nombre' => 'Reunión de Jueves'],
            ['nombre' => 'Reunión Aymara'],
            ['nombre' => 'Reunión General'],
            ['nombre' => 'Reunión de Jóvenes'],
            ['nombre' => 'Reunión de Lideres'],
            ['nombre' => 'Ensayo Alabanza Reunion Aymara'],
            ['nombre' => 'Ensayo Alabanza Reunion General'],
            ['nombre' => 'Ensayo Alabanza Reunion Jovenes'],
            ['nombre' => 'Ensayo Pandero'],
            ['nombre' => 'Limpieza Ujieres'],
            ['nombre' => 'Evangelismo'],
            ['nombre' => 'Convivencia Recreativa'],
            ['nombre' => 'Deportes y Campamentos'],
            ['nombre' => 'Teatro y Danzas Cristianas'],
            ['nombre' => 'Alabanza y Adoración'],
            ['nombre' => 'Visitas a Hospitales y Cárceles'],
            ['nombre' => 'Ayuda Social'],
            ['nombre' => 'Eventos Evangélicos'],
            ['nombre' => 'Capacitación y Liderazgo'],
            ['nombre' => 'Estudios Bíblicos'],
            ['nombre' => 'Cultos Dominicales'],
            ['nombre' => 'Oración y Ayuno'],
            ['nombre' => 'Bautismos'],
        ];

        ActividadServicio::insert($servicios);
    }
}
