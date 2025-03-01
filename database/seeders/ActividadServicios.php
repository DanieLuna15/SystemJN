<?php

namespace Database\Seeders;

use App\Models\ActividadServicio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActividadServicios extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //------------------------------------------------primera forma
        $servicios = [
            ['nombre' => 'Convivencia Recreativa', 'imagen' => null, 'estado' => true],
            ['nombre' => 'Deportes y Campamentos', 'imagen' => null, 'estado' => true],
            ['nombre' => 'Teatro y Danzas Cristianas', 'imagen' => null, 'estado' => true],
            ['nombre' => 'Alabanza y Adoración', 'imagen' => null, 'estado' => true],
            ['nombre' => 'Visitas a Hospitales y Cárceles', 'imagen' => null, 'estado' => true],
            ['nombre' => 'Ayuda Social', 'imagen' => null, 'estado' => true],
            ['nombre' => 'Eventos Evangélicos', 'imagen' => null, 'estado' => true],
            ['nombre' => 'Evangelismo', 'imagen' => null, 'estado' => true],
            ['nombre' => 'Capacitación y Liderazgo', 'imagen' => null, 'estado' => true],
            ['nombre' => 'Estudios Bíblicos', 'imagen' => null, 'estado' => true],
            ['nombre' => 'Cultos Dominicales', 'imagen' => null, 'estado' => true],
            ['nombre' => 'Oración y Ayuno', 'imagen' => null, 'estado' => true],
            ['nombre' => 'Bautismos', 'imagen' => null, 'estado' => true],
            ['nombre' => 'Reunión de Jueves', 'imagen' => null, 'estado' => true],
            ['nombre' => 'Reunión de Jóvenes', 'imagen' => null, 'estado' => true],
            ['nombre' => 'Reunión Aymara', 'imagen' => null, 'estado' => true],
            ['nombre' => 'Reunión General', 'imagen' => null, 'estado' => true],
        ];

        // Insertar en la base de datos
        foreach ($servicios as $servicio) {
            ActividadServicio::create($servicio);
        }
        // //------------------------------------------------segunda forma
        // $servicios = [
        //     ['nombre' => 'Reunión Aymara', 'imagen' => null, 'estado' => true],
        //     ['nombre' => 'Servicio de Limpieza', 'imagen' => null, 'estado' => true],
        //     ['nombre' => 'Reparación de Electrodomésticos', 'imagen' => null, 'estado' => true],
        //     ['nombre' => 'Mantenimiento de Jardines', 'imagen' => null, 'estado' => true],
        // ];
        // foreach ($servicios as $servicio) {
        //     ActividadServicio::create($servicio);
        // }
    }
}
