<?php

declare(strict_types=1);

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
use Illuminate\Support\Collection;
use Carbon\Carbon;

class MultasSheet implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles,
    WithTitle,
    WithCustomStartCell,
    WithEvents
{
    protected array $multas_detalle;
    protected array $dates;

    protected string $pageTitle;
    protected int $loopIndex = 0;
    protected ?array $headingsCache = null;

    public function __construct(array $multas_detalle, array $dates, string $pageTitle)
    {
        $this->multas_detalle = $multas_detalle;
        $this->dates = $dates;
        $this->pageTitle = $pageTitle;
    }

    public function collection(): Collection
    {
        return collect($this->multas_detalle);
    }

    public function headings(): array
    {
        if ($this->headingsCache !== null) {
            return $this->headingsCache;
        }

        // Columnas fijas iniciales
        $headerRow1 = ['N°', 'Integrantes', 'Ministerio'];
        $headerRow2 = ['', '', ''];
        $headerRow3 = ['', '', ''];

        // Recorrer las fechas y sus actividades
        foreach ($this->dates as $date) {
            $cantActividades = count($date['actividades'] ?? []);

            // ✅ Primera fila → Fecha
            $headerRow1[] = Carbon::parse($date['fecha'])->translatedFormat('d - M');
            for ($i = 1; $i < $cantActividades; $i++) {
                $headerRow1[] = '';
            }

            // ✅ Segunda fila → Día de la semana
            $headerRow2[] = $date['dia_semana'] ?? '';
            for ($i = 1; $i < $cantActividades; $i++) {
                $headerRow2[] = '';
            }

            // ✅ Tercera fila → Nombre de la actividad
            foreach ($date['actividades'] as $actividad) {
                $headerRow3[] = $actividad['nombre_actividad'];
            }
        }

        // Agregar columnas finales fijas
        $columnasFinales = [
            'Total Multas',
            'Total Productos',
            'Total a Pagar',
            'Puntualidad',
            'Pagos',
            'Observaciones'
        ];

        $headerRow1 = array_merge($headerRow1, $columnasFinales);
        $headerRow2 = array_merge($headerRow2, ['', '', '', '', '', '']);
        $headerRow3 = array_merge($headerRow3, ['', '', '', '', '', '']);

        // Almacenar en caché para eficiencia
        $this->headingsCache = [
            $headerRow1,
            $headerRow2,
            $headerRow3,
        ];

        return $this->headingsCache;
    }


    public function map($row): array
    {
        //dd($this->collection());

        $this->loopIndex++;

        //$nombreCompleto = trim(($row['integrantes'] ?? ''));
        $rowData = [
            $this->loopIndex,
            trim($row['integrantes'] ?? ''),
            $row['ministerio'] ?? '',
        ];

        foreach ($this->dates as $date) {

            foreach ($date['actividades'] as $actividad) {
                //$alias = "d_{$date['fecha']}_" . Str::slug($actividad, '_');
                $alias = "d_{$date['fecha']}_" . Str::slug($actividad['nombre_actividad'], '_'); // ✅ Correcto para arrays

                if (isset($row[$alias])) {
                    $detalle = $row[$alias]['detalle'] ?? [];
                    $tienePermiso = collect($detalle)->contains(function ($d) {
                        return $d['permiso'] !== 'No';
                    });

                    if ($tienePermiso) {
                        $value = 'Permiso';
                    } elseif (($row[$alias]['productos'] ?? 0) > 0) {
                        $value = 'Producto';
                    } else {
                        $value = (int) ($row[$alias]['multa_total'] ?? 0);
                    }
                } else {
                    $value = 'Sin datos';
                }
                $rowData[] = $value === 0 ? "0" : $value;
            }
        }

        $totalMultas = (int) ($row['Total_Multas'] ?? 0);
        $totalProductos = (int) ($row['Total_Productos'] ?? 0);
        $totalPagar = (int) ($row['Total_Pagar'] ?? 0);
        $puntualidad = $row['Puntualidad'] ?? '';
        $pagos = $row['Pagos'] ?? '';
        // Concatenación de permisos en observaciones
        $observaciones = '';

        if (!empty($row['permisos'])) {
            $observacionesArray = [];

            foreach ($row['permisos'] as $index => $permiso) {
                $fechaFormateada = Carbon::parse($permiso['fecha'])->format('d M.');

                if ($permiso['dia_entero'] == 1) {
                    // Si es un día entero (1), mostramos fecha y motivo
                    $observacion = "Fecha: {$fechaFormateada} Motivo: {$permiso['motivo']}";
                } elseif ($permiso['dia_entero'] == 2) {
                    // Si es 2, mostramos fecha, hasta y motivo
                    $hastaFormateado = $permiso['hasta'] ? Carbon::parse($permiso['hasta'])->format('d M.') : 'Sin fecha';
                    $observacion = "Fechas: ({$fechaFormateada} hasta {$hastaFormateado}) Motivo: {$permiso['motivo']}";
                } else {
                    // Si es 0, mostramos fecha, hora_inicio, hora_fin y motivo
                    $horaInicio = !empty($permiso['hora_inicio']) ? Carbon::parse($permiso['hora_inicio'])->format('H:i') : 'Sin hora';
                    $horaFin = !empty($permiso['hora_fin']) ? Carbon::parse($permiso['hora_fin'])->format('H:i') : 'Sin hora';
                    $observacion = "Fecha: {$fechaFormateada} Horas: ({$horaInicio} - {$horaFin}) Motivo: {$permiso['motivo']}";
                }

                // Agregamos numeración al inicio
                $observacionesArray[] = ($index + 1) . ". " . $observacion;
            }

            // Concatenamos todas las observaciones en una sola cadena separada por comas
            $observaciones = implode(', |', $observacionesArray);
        }



        $rowData[] = $totalMultas === 0 ? "0" : $totalMultas;
        $rowData[] = $totalProductos === 0 ? "0" : $totalProductos;
        $rowData[] = $totalPagar === 0 ? "0" : $totalPagar;
        $rowData[] = $puntualidad;
        $rowData[] = $pagos;
        $rowData[] = $observaciones;

        return $rowData;
    }


    public function styles(Worksheet $sheet): array
    {
        $headings = $this->headings();
        $lastColumnIndex = count($headings[0]);
        $lastColumn = Coordinate::stringFromColumnIndex($lastColumnIndex);

        // ✅ Ocultar las líneas de cuadrícula
        $sheet->setShowGridlines(false);

        // Estilos para las primeras 6 filas (A1 a A6)
        $headerRange = "A1:{$lastColumn}6";
        $sheet->getStyle($headerRange)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '00008B'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
        ]);

        // Estilos para la fila 6 (manteniendo texto vertical en actividades)
        $headerRange2 = "A6:{$lastColumn}6";
        $sheet->getStyle($headerRange2)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 10,
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '00008B'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                // Se mantiene el texto vertical en la fila 6
                'textRotation' => 90,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // 📌 Aplicar color de fondo si la actividad es de tipo "eventual"
        $columnIndex = 4; // Empezamos después de "N°", "Integrantes", "Ministerio"

        foreach ($this->dates as $date) {
            foreach ($date['actividades'] as $actividad) {
                // Definir la columna actual basada en la posición del índice
                $currentColumn = Coordinate::stringFromColumnIndex($columnIndex);
                $cellRange = "{$currentColumn}6"; // Rango de celda en la fila 6

                // Verificar si la actividad es de tipo "eventual" y cambiar el color
                if ($actividad['tipo'] === "eventual") {
                    $sheet->getStyle($cellRange)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => '471F03'], // Café para eventuales
                        ],
                    ]);
                }

                $columnIndex++; // Avanzar a la siguiente columna
            }
        }

        // Centrar la columna N° (columna A)
        $sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Fondo a la columna "Total Multas"
        $fixedCount = 6;
        $fixedStart = $lastColumnIndex - $fixedCount + 1;
        $totalMultasColumnLetter = Coordinate::stringFromColumnIndex($fixedStart);
        $lastRow = count($this->multas_detalle) + 6;
        $totalMultasRange = "{$totalMultasColumnLetter}7:{$totalMultasColumnLetter}{$lastRow}";
        $sheet->getStyle($totalMultasRange)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFF99'],
            ],
        ]);

        return [];
    }

    public static function afterSheet(AfterSheet $event): void
    {
        $sheet = $event->sheet->getDelegate();
        /** @var self $exportInstance */
        $exportInstance = $event->getConcernable();
        $pageTitle = $exportInstance->pageTitle ?: 'Registro de Multas';

        $headings = $exportInstance->headings();
        $lastColumnIndex = count($headings[0]);
        $lastColumn = Coordinate::stringFromColumnIndex($lastColumnIndex);
        $mergeRange = "A1:{$lastColumn}3";

        // Título
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

        // Fusiones para las columnas fijas (A, B y C)
        $sheet->mergeCells("A4:A6");
        $sheet->mergeCells("B4:B6");
        $sheet->mergeCells("C4:C6");

        // Fusiones dinámicas para cada fecha
        $colIndex = 4; // Comenzamos después de N°, Integrantes, Ministerio

        foreach ($exportInstance->dates as $date) {
            $cantActividades = count($date['actividades'] ?? []);

            if ($cantActividades > 1) {
                // ✅ Fusionar FECHA en la primera fila
                $startCol = Coordinate::stringFromColumnIndex($colIndex);
                $endCol = Coordinate::stringFromColumnIndex($colIndex + $cantActividades - 1);
                $sheet->mergeCells("{$startCol}4:{$endCol}4");

                // ✅ Fusionar DÍA DE LA SEMANA en la segunda fila
                $sheet->mergeCells("{$startCol}5:{$endCol}5");
            }

            // ✅ No fusionamos la tercera fila (Actividades), porque cada actividad debe estar en su propia celda
            $colIndex += $cantActividades;
        }


        // Fusiones para las columnas fijas (Total Multas, Total a Pagar, etc.)
        $fixedCount = 6; // 5 columnas fijas
        $fixedStart = $lastColumnIndex - $fixedCount + 1;
        for ($i = 0; $i < $fixedCount; $i++) {
            $colLetter = Coordinate::stringFromColumnIndex($fixedStart + $i);
            $sheet->mergeCells("{$colLetter}4:{$colLetter}6");
        }

        // Aplicar bordes a todo el contenido
        $lastRow = count($exportInstance->multas_detalle) + 6;
        $range = "A4:{$lastColumn}{$lastRow}";
        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => [self::class, 'afterSheet'],
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
}
