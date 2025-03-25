<?php

namespace App\Models;

use App\Constants\Status;
use App\Http\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ReglaMulta extends Model
{
    use HasFactory, GlobalStatus;
    protected $table = 'reglas_multas';
    // Clave primaria
    protected $primaryKey = 'id';

    // Timestamps automáticos
    public $timestamps = true;

    protected $fillable = [
        'descripcion',
        'multa_por_falta',
        'minutos_por_incremento',
        'multa_incremental',
        'minutos_retraso_largo',
        'multa_por_retraso_largo',
        'estado',
    ];
    public function ministerios()
    {
        return $this->belongsToMany(Ministerio::class, 'regla_multa_ministerio')->withTimestamps();;
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
