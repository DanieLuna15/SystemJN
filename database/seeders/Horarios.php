<?php

namespace Database\Seeders;

use App\Models\Horario;
use Illuminate\Database\Seeder;

class Horarios extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        //['ministerio_id'=>1, 'actividad_servicio_id'=>1,'dia_semana'=>4, 'hora_registro'=>'17:00:00', 'hora_multa'=>'19:15:00', 'tipo'=>1],

        $ministerios = [
            1 => [ // Líderes
                [1, 4, '17:00:00', '19:15:00'],
                [2, 7, '07:00:00', '07:45:00'],
                [3, 7, '10:20:00', '10:45:00'],
                [4, 7, '14:20:00', '14:45:00'],
                [5, 5, '18:00:00', '18:45:00'],
            ],
            2 => [ // Alabanza
                [1, 4, '18:00:00', '19:20:00'],
                [2, 7, '07:00:00', '07:50:00'],
                [3, 7, '10:20:00', '10:50:00'],
                [4, 7, '14:20:00', '14:50:00'],
                [6, 6, '16:00:00', '17:00:00'],
                [7, 6, '14:00:00', '15:50:00'],
                [8, 6, '13:00:00', '14:00:00'],
            ],
            3 => [ // Pandero
                [1, 4, '18:00:00', '19:20:00'],
                [2, 7, '07:00:00', '07:50:00'],
                [3, 7, '10:20:00', '10:50:00'],
                [4, 7, '14:20:00', '14:50:00'],
                [9, 6, '14:00:00', '15:00:00'],
            ],
            4 => [ // Ujieres
                [1, 4, '17:30:00', '19:20:00'],
                [2, 7, '07:00:00', '07:50:00'],
                [3, 7, '10:20:00', '10:50:00'],
                [4, 7, '14:20:00', '14:50:00'],
                [10, 6, '12:00:00', '13:00:00'],
            ],
        ];

        $horarios = [];

        foreach ($ministerios as $ministerio_id => $actividades) {
            foreach ($actividades as [$actividad_id, $dia, $registro, $multa]) {
                $horarios[] = [
                    'ministerio_id' => $ministerio_id,
                    'actividad_servicio_id' => $actividad_id,
                    'dia_semana' => $dia,
                    'hora_registro' => $registro,
                    'hora_multa' => $multa,
                    'tipo' => 1
                ];
            }
        }

        Horario::insert($horarios);
    }
}
