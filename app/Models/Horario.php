<?php

namespace App\Models;

use App\Constants\Status;
use App\Http\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Horario extends Model
{
    use HasFactory, GlobalStatus;

    protected $table = 'horarios';
    protected $fillable = ['ministerio_id','actividad_servicio_id', 'dia_semana', 'hora_registro', 'hora_multa', 'hora_limite', 'estado', 'tipo', 'fecha'];
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $casts = [
        'hora_registro' => 'string',
        'hora_multa' => 'string',
        'hora_limite' => 'string',
    ];

    public function ministerios()
    {
        return $this->belongsToMany(Ministerio::class, 'horario_ministerio')->withTimestamps();
    }

    // Relación con actividad servicio
    public function actividadServicio()
    {
        return $this->belongsTo(ActividadServicio::class, 'actividad_servicio_id', 'id');
    }

    // Accesor para obtener el día de la semana en texto
    public function diaSemanaTexto(): Attribute
    {
        return new Attribute(
            get: fn() => [
                1 => 'Lunes',
                2 => 'Martes',
                3 => 'Miércoles',
                4 => 'Jueves',
                5 => 'Viernes',
                6 => 'Sábado',
                0 => 'Domingo',
            ][$this->dia_semana] ?? 'Desconocido'
        );
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(
            get: function () {
                return $this->estado == Status::ACTIVE
                    ? '<span class="badge badge-success d-flex align-items-center justify-content-center w-100 h-100">Activo</span>'
                    : '<span class="badge badge-danger d-flex align-items-center justify-content-center w-100 h-100">Inactivo</span>';
            }
        );
    }
}
