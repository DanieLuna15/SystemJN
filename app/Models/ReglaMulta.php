<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReglaMulta extends Model
{
    use HasFactory;
    protected $table = 'reglas_multas';
    public function ministerios()
    {
        return $this->belongsToMany(Ministerio::class, 'regla_multa_ministerio', 'regla_multa_id', 'ministerio_id');
    }
}
