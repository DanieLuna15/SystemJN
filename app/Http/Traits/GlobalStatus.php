<?php

namespace App\Http\Traits;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait GlobalStatus
{
    public static function changeStatus($id, $column = 'estado')
    {
        $modelName = get_called_class(); 
        $query = $modelName::findOrFail($id);
        $column = strtolower($column);

        $query->$column = $query->$column == Status::ENABLE ? Status::DISABLE : Status::ENABLE;
        $query->save();

        $nombreModelo = class_basename($modelName);
        $mensaje = $query->$column == Status::ENABLE
            ? "El {$nombreModelo} {$query->nombre} ha sido habilitado correctamente."
            : "El {$nombreModelo} {$query->nombre} ha sido inhabilitado correctamente.";

        return redirect()->back()->with('success', $mensaje);
    }


    public function statusBadge(): Attribute
    {
        return new Attribute(
            function () {
                $html = '';
                if ($this->status == Status::ENABLE) {
                    $html = '<span class="badge badge--success">' . trans('Enabled') . '</span>';
                } else {
                    $html = '<span><span class="badge badge--warning">' . trans('Disabled') . '</span></span>';
                }
                return $html;
            }
        );
    }


    public function scopeActive($query)
    {
        return $query->where('estado', Status::ENABLE);
    }

    public function scopeInactive($query)
    {
        return $query->where('estado', Status::DISABLE);
    }
}
