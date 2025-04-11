<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Collection;
use App\Exports\MultasSheet;
use App\Exports\ExcepcionesSheet;

class ReporteExport implements WithMultipleSheets
{
    protected array $multas_detalle;
    protected array $dates;
    protected collection $excepciones;
    protected string $pageTitleMultas;
    protected string $pageTitleAsistencias;

    public function __construct(array $multas_detalle, array $asistencia_detalle, array $dates, collection $excepciones, string $pageTitleMultas, string $pageTitleAsistencias)
    {
        $this->multas_detalle = $multas_detalle;
        $this->dates = $dates;
        $this->excepciones = $excepciones;
        $this->pageTitleMultas = $pageTitleMultas;
        $this->pageTitleAsistencias = $pageTitleAsistencias;
    }

    public function sheets(): array
    {
        return [
            new MultasSheet($this->multas_detalle, $this->dates, $this->pageTitleMultas), // ✅ Hoja de registro de multas
            new ExcepcionesSheet($this->excepciones), // ✅ Hoja de excepciones
        ];
    }
}
