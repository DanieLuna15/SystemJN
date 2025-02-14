<?php

namespace App\Models;

use App\Constants\Status;
use App\Http\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActividadServicio extends Model
{
    use HasFactory, GlobalStatus;

    protected $fillable = ['nombre', 'imagen', 'estado'];
    protected $table = 'actividad_servicios';
    protected $primaryKey = 'id';
    public $timestamps = true;

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
