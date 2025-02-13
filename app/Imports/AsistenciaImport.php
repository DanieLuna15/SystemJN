<?php

namespace App\Imports;

use App\Models\Asistencia;
use Maatwebsite\Excel\Concerns\ToModel;

class AsistenciaImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Asistencia([
            //
        ]);
    }
}
