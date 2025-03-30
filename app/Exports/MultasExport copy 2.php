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
use Illuminate\Support\Str;

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
    protected $dates;       // Aquí se reciben las cabeceras dinámicas
    protected $pageTitle;
    protected $loopIndex = 0;

    public function __construct($multas_detalle, $dates, $pageTitle)
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
     * Encabezados del archivo Excel con dos filas.
     */
    public function headings(): array
    {
        // Primera fila: columnas fijas y encabezados por fecha (se combinarán celdas luego en afterSheet)
        $headerRow1 = ['N°', 'Integrantes', 'Ministerio'];
        // Segunda fila: para las actividades por fecha
        $headerRow2 = ['', '', ''];

        foreach ($this->dates as $date) {
            // Se obtiene la cantidad de actividades para la fecha
            $cantActividades = count($date['actividades']);
            // En la primera fila se coloca el encabezado de la fecha con día
            $headerRow1[] = $date['fecha'] . ' (' . $date['dia_semana'] . ')';
            // Si hay más de una actividad, se dejan las celdas siguientes en blanco
            for ($i = 1; $i < $cantActividades; $i++) {
                $headerRow1[] = '';
            }
            // En la segunda fila se muestran los nombres de las actividades
            foreach ($date['actividades'] as $actividad) {
                $headerRow2[] = $actividad;
            }
        }
        // Columnas fijas al final (se espera que se fusionen en la cabecera, similar a Blade)
        $headerRow1 = array_merge($headerRow1, ['Total Multas', 'Total a Pagar', 'Puntualidad', 'Pagos', 'Observaciones']);
        // En la segunda fila se dejan vacías las celdas de las columnas fijas
        $headerRow2 = array_merge($headerRow2, ['', '', '', '', '']);

        return [
            $headerRow1,
            $headerRow2,
        ];
    }

    /**
     * Mapeo de datos para cada fila.
     */
    public function map($row): array
    {
        $this->loopIndex++;

        // Acceder a los datos fijos (ajusta según la estructura de $multas_detalle)
        $nombreCompleto = trim(($row['nombre'] ?? '') . ' ' . ($row['apellido'] ?? ''));
        $rowData = [
            $this->loopIndex,
            $nombreCompleto,
            $row['ministerio'] ?? '',
        ];

        // Recorrer cada fecha y actividad para obtener el valor de la multa dinámica.
        foreach ($this->dates as $date) {
            foreach ($date['actividades'] as $actividad) {
                $alias = "d_{$date['fecha']}_" . Str::slug($actividad, '_');
                // Accedemos al subíndice 'multa_total'
                $value = isset($row[$alias]['multa_total']) ? (int)$row[$alias]['multa_total'] : 0;
                $rowData[] = $value === 0 ? "0" : $value;
            }
        }

        // Agregar las columnas fijas finales
        $totalMultas   = (int)($row['Total_Multas'] ?? 0);
        $totalPagar    = (int)($row['Total_Pagar'] ?? 0);
        $puntualidad   = $row['Puntualidad'] ?? '';
        $pagos         = $row['Pagos'] ?? '';
        $observaciones = $row['Observaciones'] ?? '';

        $rowData[] = $totalMultas === 0 ? "0" : $totalMultas;
        $rowData[] = $totalPagar === 0 ? "0" : $totalPagar;
        $rowData[] = $puntualidad;
        $rowData[] = $pagos;
        $rowData[] = $observaciones;

        return $rowData;
    }

    /**
     * Estilos personalizados para el documento.
     */
    public function styles(Worksheet $sheet)
    {
        $lastColumnIndex = count($this->headings()[0]); // Total de columnas según la primera fila de encabezados
        $lastColumn = Coordinate::stringFromColumnIndex($lastColumnIndex);

        // Aplicar estilos a las primeras 5 filas (A1 a A5) para la cabecera:
        // - Fondo azul
        // - Bordes blancos
        $headerRange = "A1:{$lastColumn}5";
        $sheet->getStyle($headerRange)->applyFromArray([
            'fill' => [
                'fillType'       => Fill::FILL_SOLID,
                'startColor'     => ['argb' => '00008B'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFFFFFFF'], // Bordes blancos
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'bold'  => true,
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
        ]);

        // Aplicar estilos a los datos (desde la fila 6 en adelante) con bordes negros
        $lastRow = count($this->multas_detalle) + 5;
        $dataRange = "A6:{$lastColumn}{$lastRow}";
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FF000000'], // Bordes negros
                ],
            ],
        ]);

        // Asegurar que la columna A (N°) esté centrada
        $sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }

    /**
     * Configuración del título y diseño del encabezado (A1 a A3) y fusiones de celdas.
     */
    public static function afterSheet(AfterSheet $event)
    {
        $sheet           = $event->sheet->getDelegate();
        $exportInstance  = $event->getConcernable();
        $pageTitle       = $exportInstance->pageTitle ?? 'Registro de Multas';

        $lastColumnIndex = count($exportInstance->headings()[0]);
        $lastColumn      = Coordinate::stringFromColumnIndex($lastColumnIndex);
        $mergeRange      = "A1:{$lastColumn}3";

        // Título: se mantiene el fondo azul y la configuración ya establecida en styles()
        $sheet->setCellValue('A1', $pageTitle);
        $sheet->mergeCells($mergeRange);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'size'  => 20,
                'name'  => 'Lucida Calligraphy',
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => '00008B'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            // No se modifica el borde aquí para no alterar lo definido en styles()
        ]);

        // Insertar logo (ajusta la ruta según corresponda)
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path('vendor/adminlte/dist/img/logo.jpg'));
        $drawing->setHeight(50);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(5);
        $drawing->setOffsetY(5);
        $drawing->setWorksheet($sheet);

        // Fusiones para los encabezados dinámicos (filas 4 y 5)
        $sheet->mergeCells("A4:A5");
        $sheet->mergeCells("B4:B5");
        $sheet->mergeCells("C4:C5");

        $colIndex = 4; // Comienza en la columna 4 (las columnas A, B y C ya están ocupadas)
        foreach ($exportInstance->dates as $date) {
            $cant = count($date['actividades']);
            if ($cant > 1) {
                $startCol = Coordinate::stringFromColumnIndex($colIndex);
                $endCol   = Coordinate::stringFromColumnIndex($colIndex + $cant - 1);
                $sheet->mergeCells("{$startCol}4:{$endCol}4");
            }
            $colIndex += $cant;
        }

        // Fusiones para las columnas fijas al final (en la cabecera, filas 4 y 5)
        $fixedStart = $lastColumnIndex - 4 + 1;
        $startFixed = Coordinate::stringFromColumnIndex($fixedStart);
        $sheet->mergeCells("{$startFixed}4:{$startFixed}5"); // Total Multas
        $sheet->mergeCells(Coordinate::stringFromColumnIndex($fixedStart + 1) . "4:" . Coordinate::stringFromColumnIndex($fixedStart + 1) . "5"); // Total a Pagar
        $sheet->mergeCells(Coordinate::stringFromColumnIndex($fixedStart + 2) . "4:" . Coordinate::stringFromColumnIndex($fixedStart + 2) . "5"); // Puntualidad
        $sheet->mergeCells(Coordinate::stringFromColumnIndex($fixedStart + 3) . "4:" . Coordinate::stringFromColumnIndex($fixedStart + 3) . "5"); // Pagos
        $sheet->mergeCells(Coordinate::stringFromColumnIndex($fixedStart + 4) . "4:" . Coordinate::stringFromColumnIndex($fixedStart + 4) . "5"); // Observaciones

        // No se aplica borde general desde afterSheet, ya que los estilos se definieron en styles()
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

