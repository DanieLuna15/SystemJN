<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HorariosSeeder extends Seeder
{
    public function run()
    {
        $horarios = collect([
            ['dia_semana' => 4, 'hora_registro' => '16:00:00', 'hora_multa' => '19:15:00', 'hora_limite' => '21:00:00', 'actividad_servicio_id' => 1, 'tipo_pago' => 1],
            ['dia_semana' => 0, 'hora_registro' => '07:00:00', 'hora_multa' => '07:45:00', 'hora_limite' => '10:00:00', 'actividad_servicio_id' => 2, 'tipo_pago' => 1],
            ['dia_semana' => 0, 'hora_registro' => '10:20:00', 'hora_multa' => '10:45:00', 'hora_limite' => '13:00:00', 'actividad_servicio_id' => 3, 'tipo_pago' => 1],
            ['dia_semana' => 0, 'hora_registro' => '15:00:00', 'hora_multa' => '15:45:00', 'hora_limite' => '17:30:00', 'actividad_servicio_id' => 4, 'tipo_pago' => 1],
            ['dia_semana' => 5, 'hora_registro' => '19:00:00', 'hora_multa' => '19:30:00', 'hora_limite' => '21:00:00', 'actividad_servicio_id' => 5, 'tipo_pago' => 0],
            ['dia_semana' => 6, 'hora_registro' => '12:00:00', 'hora_multa' => '14:00:00', 'hora_limite' => '15:00:00', 'actividad_servicio_id' => 10, 'tipo_pago' => 1],
            ['dia_semana' => 6, 'hora_registro' => '14:00:00', 'hora_multa' => '15:00:00', 'hora_limite' => '16:00:00', 'actividad_servicio_id' => 9, 'tipo_pago' => 1],
            ['dia_semana' => 6, 'hora_registro' => '13:00:00', 'hora_multa' => '14:00:00', 'hora_limite' => '15:00:00', 'actividad_servicio_id' => 8, 'tipo_pago' => 1],
            ['dia_semana' => 6, 'hora_registro' => '14:00:00', 'hora_multa' => '15:00:00', 'hora_limite' => '16:00:00', 'actividad_servicio_id' => 7, 'tipo_pago' => 1],
            ['dia_semana' => 6, 'hora_registro' => '15:30:00', 'hora_multa' => '16:30:00', 'hora_limite' => '18:00:00', 'actividad_servicio_id' => 6, 'tipo_pago' => 1],
        ])->map(function ($horario) {
            return array_merge($horario, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        })->toArray();

        DB::table('horarios')->insert($horarios);
    }
}
