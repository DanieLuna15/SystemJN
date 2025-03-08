<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

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
            'N°',
            'INTEGRANTES',
            '1-ene',
            '2-ene',
            '5-ene',
            '9-ene',
            '12-ene',
            '16-ene',
            '19-ene',
            '23-ene',
            '26-ene',
            '30-ene',
            'Total General',
            'Total a pagar',
            'Puntualidad',
            'Pagos',
            'OBSERVACIONES',

        ];
    }

    public function map($row): array
    {
        return [

            isset($row->id) ? $row->id : null,
            isset($row->emp_firstname) ? $row->emp_firstname . ' ' . $row->emp_lastname : null,
            isset($row->multa_1_ene) ? $row->multa_1_ene : null,
            isset($row->multa_2_ene) ? $row->multa_2_ene : null,
            isset($row->multa_5_ene) ? $row->multa_5_ene : null,
            isset($row->multa_9_ene) ? $row->multa_9_ene : null,
            isset($row->multa_12_ene) ? $row->multa_12_ene : null,
            isset($row->multa_16_ene) ? $row->multa_16_ene : null,
            isset($row->multa_19_ene) ? $row->multa_19_ene : null,
            isset($row->multa_23_ene) ? $row->multa_23_ene : null,
            isset($row->multa_26_ene) ? $row->multa_26_ene : null,
            isset($row->multa_30_ene) ? $row->multa_30_ene : null,
            isset($row->total_general) ? $row->total_general : null,
            isset($row->total_pagar) ? $row->total_pagar : null,
            isset($row->puntualidad) ? $row->puntualidad : null,
            isset($row->pagos) ? $row->pagos : null,
            isset($row->observaciones) ? $row->observaciones : null,


        ];
    }

    public function styles(Worksheet $sheet)
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
            //             // Estilo para las columna
            // 'C,D,G' => [
            //             'alignment' => [
            //                 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            //                 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            //                 'textRotation' => 90, // Rota el texto en 90 grados
            //             ]
            //            ],

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
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->mergeCells('A4:A7');
        $sheet->getStyle('A4:A7')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => [
                        'argb' => Color::COLOR_BLACK,
                    ],
                ],
            ],
        ]);
        $sheet->setCellValue('A4', 'N°');

        $sheet->mergeCells('B4:B7');
        $sheet->getStyle('B4:B7')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->setCellValue('B4', 'INTEGRANTES');

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path('vendor/adminlte/dist/img/logo.jpg'));
        $drawing->setHeight(50);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(10);
        $drawing->setOffsetY(10);
        $drawing->setWorksheet($sheet);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => [self::class, 'afterSheet'],
        ];
    }
}
