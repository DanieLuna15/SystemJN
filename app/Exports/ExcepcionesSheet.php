<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcepcionesSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected Collection $excepciones;

    public function __construct(Collection $excepciones)
    {
        $this->excepciones = $excepciones;
    }

    public function collection()
    {
        return $this->excepciones->map(function ($row, $index) {
            return [
                'N°' => $index + 1,
                'Motivo' => $row['motivo'] ?? '',
                'Fecha' => \Carbon\Carbon::parse($row['fecha'])->format('d/m/Y'), // ✅ Formato día/mes/año
                'Hasta' => $row['hasta'] ? \Carbon\Carbon::parse($row['hasta'])->format('d/m/Y') : 'No especificado',
                'Día Entero' => match ($row['dia_entero'] ?? 0) {
                    1 => 'Todo el día',        // ✅ Cubre todo el día
                    2 => 'Varios días', // ✅ Indica un período extendido
                    default => 'Rango de horas',  // ✅ No cubre todo el día
                },
                'Hora Inicio' => $row['hora_inicio'] ?? 'Sin hora',
                'Hora Fin' => $row['hora_fin'] ?? 'Sin hora',
                'Usuario' => $row->usuario->name ?? 'Desconocido', // ✅ Muestra el nombre en lugar del ID
                'Creado el' => \Carbon\Carbon::parse($row['created_at'])->format('d/m/Y H:i:s'), // ✅ Fecha con hora
            ];
        });
    }


    public function headings(): array
    {
        return [
            ['REGISTRO DE EXCEPCIONES'], // ✅ Título de la tabla en la primera fila
            [
                'N°',
                'Motivo',
                'Fecha',
                'Hasta',
                'Tipo',
                'Hora Inicio',
                'Hora Fin',
                'Autor',
                'Fecha de Creación'
            ]
        ];
    }

    public function title(): string
    {
        return 'Excepciones';
    }

    public function styles(Worksheet $sheet)
    {
        // ✅ Obtener el número de filas dinámicamente
        $totalRows = $this->excepciones->count() + 2; // Sumamos 2 por el título y los encabezados

        // ✅ Combinar celdas de A1 a I1 (Titulo)
        $sheet->mergeCells('A1:I1');

        // ✅ Ajustar ancho automático de columnas
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // ✅ Ocultar las líneas de cuadrícula
        $sheet->setShowGridlines(false);

        // ✅ Aplicar estilos al título y encabezados
        return [
            1 => [ // Estilo del título
                'font' => ['bold' => true, 'size' => 16, 'name' => 'Lucida Handwriting', 'color' => ['rgb' => 'FFFFFF']], // Texto blanco
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'], // Centrado
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '0A1F44']], // Fondo azul oscuro
            ],
            2 => [ // Estilo de los encabezados
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']], // Texto blanco
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'], // Centrado
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '0A1F44']], // Fondo azul oscuro
                'borders' => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'FFFFFF']]], // Bordes blancos
            ],
            "A3:I$totalRows" => [ // ✅ Bordes dinámicos según el número de filas
                'font' => ['size' => 10],
                'borders' => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => '000000']]],
            ],
        ];
    }
}
