<?php

namespace Database\Seeders;

use Database\Seeders\HorariosSeeder;
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
            UsersDefaultSeeder::class,
            UsersSeeder::class,
            ConfiguracionSeeder::class,
            ActividadServiciosSeeder::class,
            MinisterioSeeder::class,
            HorariosSeeder::class,
            HorarioMinisterioSeeder::class,
            MinisterioUserSeeder::class,
            MinisterioLiderSeeder::class,
            ReglasMultasSeeder::class,
            ReglaMultaMinisterioSeeder::class,
        ]);
    }
}
