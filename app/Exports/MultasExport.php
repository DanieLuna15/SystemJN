<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class MultasExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles,
    WithTitle,
    WithCustomStartCell,
    WithEvents
{
    protected $multas_detalle;
    private $loopIndex = 0; // Contador para la columna N°

    public function __construct($multas_detalle)
    {
        $this->multas_detalle = $multas_detalle;
    }

    /**
     * Recuperar la colección de datos.
     */
    public function collection()
    {
        return collect($this->multas_detalle);
    }

    /**
     * Encabezados del archivo Excel.
     */
    public function headings(): array
    {
        return [
            'N°',
            'Nombre',
            'Apellido',
            'Departamento',
            'Fecha',
            'Hora',
            'Día',
            'Multa (Bs)',
        ];
    }

    /**
     * Mapeo de datos para cada fila.
     */
    public function map($row): array
    {
        $this->loopIndex++; // Incrementar el contador en cada iteración

        return [
            $this->loopIndex,              // N° consecutivo
            $row->emp_firstname ?? '',     // Nombre
            $row->emp_lastname ?? '',      // Apellido
            $row->dept_name ?? '',         // Departamento
            $row->punch_date ?? '',        // Fecha de Marcación
            $row->punch_hour ?? '',        // Hora de Marcación
            $row->dia_semana ?? '',        // Día de la Semana
            $row->multa_bs ?? '',          // Multa en Bs
        ];
    }

    /**
     * Estilos personalizados.
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A4:H4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '00008B'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Estilo para centrar el texto de la columna N°
        $sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }

    /**
     * Configuración de título y diseño del encabezado.
     */
    public static function afterSheet(AfterSheet $event)
    {
        $sheet = $event->sheet->getDelegate();

        // Estilo del título
        $sheet->setCellValue('A1', 'Registro de Multas');
        $sheet->mergeCells('A1:H3');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 20,
                'name' => 'Lucida Calligraphy',
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '00008B'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Insertar logo
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path('vendor/adminlte/dist/img/logo.jpg')); // Asegúrate de que la ruta sea válida.
        $drawing->setHeight(50); // Ajusta el tamaño del logo.
        $drawing->setCoordinates('A1'); // Posición del logo.
        $drawing->setOffsetX(10);
        $drawing->setOffsetY(10);
        $drawing->setWorksheet($sheet);
    }

    /**
     * Registrar eventos personalizados.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => [self::class, 'afterSheet'],
        ];
    }

    /**
     * Título de la hoja.
     */
    public function title(): string
    {
        return 'Registro de Multas';
    }

    /**
     * Celda de inicio.
     */
    public function startCell(): string
    {
        return 'A4';
    }
}
