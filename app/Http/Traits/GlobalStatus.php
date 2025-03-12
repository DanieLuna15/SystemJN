<?php

namespace App\Http\Traits;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait GlobalStatus
{
    /**
     * Cambia el estado de un modelo (habilitar/deshabilitar).
     *
     * @param  int  $id  El ID del modelo.
     * @param  string  $column  El nombre de la columna que se va a actualizar (por defecto 'estado').
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function changeStatus($id, $column = 'estado')
    {
        $modelName = static::class; // Usar ::class para obtener el nombre de la clase
        $query = $modelName::findOrFail($id);

        // Validar la columna
        $validColumns = ['estado']; // Puedes agregar más columnas aquí si lo deseas
        if (!in_array(strtolower($column), $validColumns)) {
            throw new \InvalidArgumentException("El campo '{$column}' no es válido.");
        }

        // Cambiar el estado
        $query->$column = $query->$column == Status::ENABLE ? Status::DISABLE : Status::ENABLE;
        $query->save();

        // Generar el mensaje dinámico
        $nombreModelo = class_basename($modelName);
        $mensaje = $query->$column == Status::ENABLE
            ? "El/La {$nombreModelo} {$query->nombre} ha sido habilitado correctamente."
            : "El/La {$nombreModelo} {$query->nombre} ha sido inhabilitado correctamente.";

        return redirect()->back()->with('success', $mensaje);
    }

    /**
     * Obtiene el badge de estado (habilitado/deshabilitado).
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function statusBadge(): Attribute
    {
        return new Attribute(
            get: function () {
                return $this->estado == Status::ENABLE
                    ? '<span class="badge badge--success">' . trans('Enabled') . '</span>'
                    : '<span class="badge badge--warning">' . trans('Disabled') . '</span>';
            }
        );
    }

    /**
     * Scope para obtener los registros habilitados.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('estado', Status::ENABLE);
    }

    /**
     * Scope para obtener los registros deshabilitados.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('estado', Status::DISABLE);
    }
}
