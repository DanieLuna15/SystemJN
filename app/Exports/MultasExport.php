<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class MultasExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle, WithCustomStartCell, WithEvents
{
    protected $multas_detalle;

    public function __construct($multas_detalle)
    {
        $this->multas_detalle = $multas_detalle;
    }

    public function collection()
    {
        return collect($this->multas_detalle);
    }

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
        $sheet->getStyle('A4:R4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'name' => 'Times New Roman',
                'color' => [
                    'argb' => Color::COLOR_WHITE,
                ],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => '00008B',
                ],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        return [
            'A' => [
                'font' => ['bold' => true],
            ],
        ];
    }

    public function title(): string
    {
        return 'Registro de Multas';
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public static function afterSheet(AfterSheet $event)
    {
        $sheet = $event->sheet->getDelegate();

        $sheet->setCellValue('A1', 'Registro de Multas');
        $sheet->mergeCells('A1:R3');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 22,
                'name' => 'Lucida Calligraphy',
                'color' => [
                    'argb' => Color::COLOR_WHITE,
                ],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => '00008B',
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->mergeCells('A4:A7');
        $sheet->getStyle('A4:A7')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => [
                        'argb' => Color::COLOR_BLACK,
                    ],
                ],
            ],
        ];
    }
}
