<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReporteExport implements WithMultipleSheets
{
    protected array $multas_detalle;
    protected array $dates;
    protected \Illuminate\Support\Collection $excepciones;
    protected string $pageTitle;

    public function __construct(array $multas_detalle, array $dates, \Illuminate\Support\Collection $excepciones, string $pageTitle)
    {
        $this->multas_detalle = $multas_detalle;
        $this->dates = $dates;
        $this->excepciones = $excepciones;
        $this->pageTitle = $pageTitle;
    }

    public function sheets(): array
    {
        return [
            new MultasSheet($this->multas_detalle, $this->dates, $this->pageTitle), // ✅ Hoja de registro de multas
            new ExcepcionesSheet($this->excepciones), // ✅ Hoja de excepciones
        ];
    }
}
