<?php

namespace App\Models;

use App\Constants\Status;
use App\Http\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Permiso extends Model
{
    use HasFactory, GlobalStatus;

    // Tabla asociada
    protected $table = 'permisos';

    // Clave primaria
    protected $primaryKey = 'id';

    // Timestamps automáticos
    public $timestamps = true;

    // Campos que pueden ser llenados en masa
    protected $fillable = [
        'user_id',
        'fecha',
        'hasta',
        'dia_entero',
        'hora_inicio',
        'hora_fin',
        'motivo',
        'estado',
    ];

    /**
     * Relación con el usuario que solicita el permiso.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación muchos a muchos con Usuarios a través de la tabla intermedia permiso_usuario.
     */
    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'permiso_usuario', 'permiso_id', 'usuario_id')->withTimestamps();
    }


    /**
     * Método para generar el badge de estado en HTML.
     */
    public function statusBadge(): Attribute
    {
        return new Attribute(
            get: function () {
                switch ($this->estado) {
                    case 0:
                        return '<span class="badge badge-warning d-flex align-items-center justify-content-center w-100 h-100">Pendiente</span>';
                    case 1:
                        return '<span class="badge badge-success d-flex align-items-center justify-content-center w-100 h-100">Autorizado</span>';
                    case 2:
                        return '<span class="badge badge-danger d-flex align-items-center justify-content-center w-100 h-100">Rechazado</span>';
                    default:
                        return '<span class="badge badge-secondary d-flex align-items-center justify-content-center w-100 h-100">Desconocido</span>';
                }
            }
        );
    }

}
