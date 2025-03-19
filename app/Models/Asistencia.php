<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Asistencia extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'asistencias';

    // Campos asignables
    protected $fillable = ['ci', 'fecha', 'hora_marcacion'];

    /**
     * Relación con el modelo User a través del campo 'ci'
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'ci', 'ci'); // Relación usando el campo 'ci'
    }
}
