<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\MinisterioSeeder;
use Database\Seeders\ActividadServiciosSeeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Llamar a los seeders individuales
        $this->call([
            RolesPermissionsSeeder::class,
            UsersSeeder::class,
            ConfiguracionSeeder::class,
            ActividadServicios::class,
            MinisterioSeeder::class,
        ]);
    }
}
