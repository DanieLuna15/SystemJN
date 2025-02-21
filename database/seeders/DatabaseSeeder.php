<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Llamar a los seeders individuales
        $this->call([
            RolesPermissionsSeeder::class,
            UsersSeeder::class,
            ConfiguracionSeeder::class
        ]);
    }
}
