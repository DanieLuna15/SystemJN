<?php

namespace Database\Seeders;

use App\Models\Configuracion;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ConfiguracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Configuracion::create([
            'nombre' => 'Jehová Nissi',
            'descripcion' => 'Congregación de Jehová Nissi',
            'direccion' => 'Calle 123',
            'telefono' => '123456789',
            'email' => 'admin@empresa.com',
            'url' => 'https://miempresa.com',
            'logo' => 'images/default-dark.png',
            'favicon' => 'images/default-favicon.png'
        ]);
    }
}
