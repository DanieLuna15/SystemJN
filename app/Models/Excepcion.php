<?php

namespace App\Models;

use App\Constants\Status;
use App\Http\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Excepcion extends Model
{
    use HasFactory, GlobalStatus;

    // Tabla asociada
    protected $table = 'excepciones';

    // Clave primaria
    protected $primaryKey = 'id';

    // Timestamps automáticos
    public $timestamps = true;

    // Campos que pueden ser llenados en masa
    protected $fillable = [
        'usuario_id',
        'fecha',
        'hasta',
        'dia_entero',
        'hora_inicio',
        'hora_fin',
        'motivo',
    ];

    /**
     * Relación muchos a muchos con Ministerios a través de la tabla intermedia excepcion_ministerio.
     */
    public function ministerios()
    {
        return $this->belongsToMany(Ministerio::class, 'excepcion_ministerio')->withTimestamps();
    }

    /**
     * Método para generar el badge de estado en HTML.
     */
    public function statusBadge(): Attribute
    {
        return new Attribute(
            get: function () {
                return $this->dia_entero
                    ? '<span class="badge badge-success d-flex align-items-center justify-content-center w-100 h-100">Día Entero</span>'
                    : '<span class="badge badge-warning d-flex align-items-center justify-content-center w-100 h-100">Horario</span>';
            }
        );
    }
}
