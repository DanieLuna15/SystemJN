<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportacionAsistencia extends Model
{
    use HasFactory;
    
    protected $table = 'importaciones_asistencia';
    protected $fillable = ['archivo', 'ruta', 'usuario_id', 'estado', 'errores'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
