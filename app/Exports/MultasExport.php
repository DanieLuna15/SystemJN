<?php

namespace App\Exports;

use App\Models\Configuracion;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MultasExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $multas_detalle;

    public function __construct($multas_detalle)
    {
        $this->multas_detalle = $multas_detalle;
    }

    // Obtener la colección de datos
    public function collection()
    {
        return collect($this->multas_detalle);
    }

    // Definir los encabezados de las columnas
    public function headings(): array
    {
        return [
            'Nombre', 
            'Apellido', 
            'Ministerio', 
            'Dia de la semana', 
            'Fecha',
            'Hora de Ingreso', 
            'Multa'
        ];
    }

    // Mapear los datos a las columnas correspondientes
    public function map($row): array
    {
        return [
            $row->emp_firstname,
            $row->emp_lastname,
            $row->dept_name,
            $row->dia_semana,
            $row->punch_date,
            $row->punch_hour,
            $row->multa_bs
        ];
    }

    // Aplicar estilos (en este caso, color de fondo a la fila de encabezado)
    public function styles($sheet)
    {
        return [
            // Estilo para la fila de encabezado
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => Color::COLOR_BLUE
                    ]
                ]
            ],
        ];
    }
}
