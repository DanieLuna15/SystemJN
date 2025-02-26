<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'logo', 'favicon', 'loader', 'direccion', 'telefono', 'email', 'urdescripcion'];
    protected $table = 'configuracions';
    protected $primaryKey = 'id';
    public $timestamps = true;
}
