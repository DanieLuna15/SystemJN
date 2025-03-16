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
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

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
    protected $dates;       // Declarar la propiedad $dates
    protected $pageTitle;
    protected $loopIndex = 0;

    public function __construct($multas_detalle,$dates, $pageTitle)
    {
        $this->multas_detalle = $multas_detalle;
        $this->dates = $dates;
        $this->pageTitle = $pageTitle;
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
        $headings = [
            'N°',
            'Integrantes', // Se une nombre y apellido en una sola columna
            'Ministerio'
        ];

        foreach ($this->dates as $date) {
            $headings[] = $date['dia_semana_lit'] . ' (' . $date['fecha'] . ')';
        }

        $headings[] = 'Total Multas';
        $headings[] = 'Total a Pagar';
        $headings[] = 'Puntualidad';
        $headings[] = 'Pagos';
        $headings[] = 'Observaciones';

        return $headings;
    }

    /**
     * Mapeo de datos para cada fila.
     */
    public function map($row): array
    {
        $this->loopIndex++;

        // Concatenar Nombre y Apellido
        $nombreCompleto = trim(($row->emp_firstname ?? '') . ' ' . ($row->emp_lastname ?? ''));

        $rowData = [
            $this->loopIndex,
            $nombreCompleto, // Se usa la nueva columna "Nombre Completo"
            $row->dept_name ?? ''
        ];

        foreach ($this->dates as $date) {
            $alias = $date['alias'];
            $value = (int)($row->{$alias} ?? 0);
            $rowData[] = $value === 0 ? "0" : $value;
        }

        // Agregar las columnas adicionales asegurando que tengan valores por defecto
        $totalMultas = (int)($row->Total_Multas ?? 0);
        $totalPagar = (int)($row->Total_Pagar ?? 0);
        $puntualidad = $row->Puntualidad ?? '';
        $pagos = $row->Pagos ?? '';
        $observaciones = $row->Observaciones ?? '';

        // Asegurar que los valores numéricos se muestren correctamente
        $rowData[] = $totalMultas === 0 ? "0" : $totalMultas;
        $rowData[] = $totalPagar === 0 ? "0" : $totalPagar;
        $rowData[] = $puntualidad;
        $rowData[] = $pagos;
        $rowData[] = $observaciones;

        return $rowData;
    }


    /**
     * Estilos personalizados.
     */
    public function styles(Worksheet $sheet)
    {
        // Determinar cuántas columnas hay
        $lastColumnIndex = count($this->headings()); // Total de columnas
        $lastColumn = Coordinate::stringFromColumnIndex($lastColumnIndex); // Convertir a letra de columna

        // Estilos para las celdas del encabezado
        $range = "A4:{$lastColumn}4"; // Ajusta dinámicamente el rango para el encabezado

        // Aplicar estilos generales para el encabezado
        $sheet->getStyle($range)->applyFromArray([
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

        // Centrar la columna N° (Columna de índice)
        $sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Estilo para las fechas dinámicas en la cabecera, ponerlas en vertical
        $dateStartColumn = 3; // El índice de la primera columna de las fechas (A es 1, B es 2, C es 3)
        $dateEndColumn = $lastColumnIndex - 1; // Para obtener la última columna de fechas dinámicas
        for ($i = $dateStartColumn; $i <= $dateEndColumn; $i++) {
            $columnLetter = Coordinate::stringFromColumnIndex($i);
            $sheet->getStyle("{$columnLetter}4")->getAlignment()->setTextRotation(90); // Rotar el texto 90 grados para vertical
        }

        // Aplicar color de fondo amarillo claro a la columna de Total Multas
        $totalMultasColumn = $lastColumnIndex - 4; // Total Multas está 4 columnas antes de la última columna
        $totalMultasColumnLetter = Coordinate::stringFromColumnIndex($totalMultasColumn);
        $totalMultasRange = "{$totalMultasColumnLetter}5:{$totalMultasColumnLetter}" . (count($this->multas_detalle) + 4); // Rango de la columna Total Multas

        $sheet->getStyle($totalMultasRange)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFF99'], // Color de relleno amarillo claro
            ],
        ]);

        return [];
    }






    /**
     * Configuración de título y diseño del encabezado.
     */
    public static function afterSheet(AfterSheet $event)
    {
        $sheet = $event->sheet->getDelegate();
        $exportInstance = $event->getConcernable();

        // Recuperar el título desde el evento o usar un valor por defecto
        $pageTitle = $exportInstance->pageTitle ?? 'Registro de Multas';

        $lastColumnIndex = count($exportInstance->headings());
        $lastColumn = Coordinate::stringFromColumnIndex($lastColumnIndex); // Última columna dinámica
        $mergeRange = "A1:{$lastColumn}3"; // Rango de combinación de celdas

        // Estilo del título
        $sheet->setCellValue('A1', $pageTitle);
        $sheet->mergeCells($mergeRange);
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
        $drawing->setHeight(50);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(5);
        $drawing->setOffsetY(5);
        $drawing->setWorksheet($sheet);

        // Aplicar bordes a todo el contenido
        $lastRow = count($exportInstance->collection()) + 4; // El número de filas con datos, considerando las filas de encabezado
        $range = "A4:{$lastColumn}{$lastRow}"; // Rango de celdas con los datos

        // Aplicar bordes a las celdas
        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);
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
