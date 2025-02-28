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
            ['nombre' => 'Reunión Aymara', 'imagen' => null],
            ['nombre' => 'Servicio de Limpieza','imagen' => null],
            ['nombre' => 'Reparación de Electrodomésticos','imagen' => null],
            ['nombre' => 'Mantenimiento de Jardines', 'imagen' => null],
        ];

        collect($servicios)->each(fn($servicio) => ActividadServicio::create([
            ...$servicio, 
            'estado' => true
        ]));


        //------------------------------------------------segunda forma
        $servicios = [
            ['nombre' => 'Reunión Aymara', 'imagen' => null, 'estado' => true],
            ['nombre' => 'Servicio de Limpieza', 'imagen' => null, 'estado' => true],
            ['nombre' => 'Reparación de Electrodomésticos', 'imagen' => null, 'estado' => true],
            ['nombre' => 'Mantenimiento de Jardines', 'imagen' => null, 'estado' => true],
        ];
        foreach ($servicios as $servicio) {
            ActividadServicio::create($servicio);
        }
    }
}
