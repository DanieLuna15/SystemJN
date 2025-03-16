<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Horario;
use Illuminate\Http\Request;
use App\Exports\MultasExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{

    public function exportarReporte(Request $request)
    {
        // Recuperar el rango de fechas desde la sesión o el request
        $dateRange = $request->session()->get('date_range', now()->startOfMonth()->format('d-m-Y 00:00:00') . ' - ' . now()->endOfMonth()->format('d-m-Y 23:59:59'));

        [$startDate, $endDate] = explode(' - ', $dateRange);

        // Convertir las fechas a formato adecuado
        $startDate = Carbon::createFromFormat('d-m-Y H:i:s', trim($startDate))->format('Y-m-d H:i:s');
        $endDate = Carbon::createFromFormat('d-m-Y H:i:s', trim($endDate))->format('Y-m-d H:i:s');
        $deptId = $request->input('ministerio_id', 3);

        // Paso 1: Obtener las fechas únicas dentro del rango para los días de interés (jueves=4, viernes=5, domingo=0)
        $datesResult = DB::connection('sqlite')->select("
            SELECT DISTINCT DATE(punch_time) AS fecha, strftime('%w', punch_time) AS dia_semana
            FROM att_punches
            WHERE punch_time BETWEEN ? AND ?
            AND strftime('%w', punch_time) IN ('0', '4', '5')
            ORDER BY fecha ASC
        ", [$startDate, $endDate]);

        $dayNames = [
            '0' => 'Domingo',
            '1' => 'Lunes',
            '2' => 'Martes',
            '3' => 'Miércoles',
            '4' => 'Jueves',
            '5' => 'Viernes',
            '6' => 'Sábado'
        ];

        $dates = [];
        foreach ($datesResult as $row) {
            $dates[] = [
                'fecha'      => $row->fecha,
                'alias'      => 'd_' . str_replace('-', '_', $row->fecha),
                'dia_semana' => $row->dia_semana,
                'dia_semana_lit' => $dayNames[$row->dia_semana]
            ];
        }

        // Paso 2: Construir dinámicamente las columnas del SELECT para cada fecha
        $columnsSql = [];
        $sumColumns = []; // Para construir la suma total de multas

        foreach ($dates as $d) {
            $fecha = $d['fecha'];
            $alias = $d['alias'];
            $dia   = $d['dia_semana']; // '0' (Dom), '4' (Jue), '5' (Vie)

            if ($dia == '5') {
                // Para viernes: se contempla la excepción del 21 de febrero y la lógica general.
                $col = "SUM(
                    CASE
                        WHEN DATE(marc.punch_time) = '$fecha' THEN (
                            CASE
                                WHEN '$fecha' = '2025-02-21' THEN
                                    CASE
                                        WHEN TIME(marc.punch_time) > '22:15:59' THEN
                                            CASE
                                                WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 22:15:59')) > 1800
                                                    THEN 20  -- Multa de 20 Bs si el retraso es mayor a 30 minutos
                                                    ELSE ((CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 22:15:59')) / 300 AS INTEGER) + 1) * 2)  -- Multa progresiva de 2 Bs por cada 5 minutos de retraso
                                            END
                                        ELSE 0  -- No se aplica multa si llega antes de las 22:15:59 en el 21 de febrero
                                    END
                                ELSE
                                    CASE
                                        WHEN TIME(marc.punch_time) > '19:30:59' THEN
                                            0 -- 'ACA DEBERIA PRODUCTO'
                                        ELSE
                                            0
                                    END
                            END
                        ) ELSE 0
                    END
                ) AS \"$alias\"";
            } elseif ($dia == '4') {
                // Para jueves:
                // - Si hay marcación y es después de las 19:15:59 se calcula la multa según la lógica original.
                // - Además, se agrega una penalización de 40 Bs si NO hay ninguna marcación en el rango de 17:00:00 a 21:00:00.
                $col = "(
                    SUM(
                        CASE WHEN DATE(marc.punch_time) = '$fecha' THEN (
                            CASE
                                WHEN TIME(marc.punch_time) > '19:15:59' THEN
                                    CASE WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 19:15:59')) > 1800
                                        THEN 20
                                        ELSE ((CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 19:15:59')) / 300 AS INTEGER) + 1) * 2)
                                    END
                                ELSE 0
                            END
                        ) ELSE 0 END
                    )
                    +
                    CASE WHEN (
                        SELECT COUNT(*) FROM att_punches a2
                        WHERE a2.emp_id = marc.emp_id
                          AND DATE(a2.punch_time) = '$fecha'
                          AND TIME(a2.punch_time) BETWEEN '16:00:00' AND '21:00:00'
                    ) = 0 THEN 40 ELSE 0 END
                ) AS \"$alias\"";
            }
            elseif ($dia == '0') {
                $col = "(
                    (
                        SUM(
                            CASE WHEN DATE(marc.punch_time) = '$fecha'
                                 AND TIME(marc.punch_time) BETWEEN '07:45:59' AND '10:00:00'
                                 THEN CASE WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 07:45:59')) > 1800
                                      THEN 20
                                      ELSE ((CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 07:45:59')) / 300 AS INTEGER) + 1) * 2)
                                 END ELSE 0 END
                        )
                        +
                        CASE WHEN (
                            SELECT COUNT(*) FROM att_punches a2
                            WHERE a2.emp_id = marc.emp_id
                              AND DATE(a2.punch_time) = '$fecha'
                              AND TIME(a2.punch_time) BETWEEN '07:00:00' AND '10:00:00'
                        ) = 0 THEN 40 ELSE 0 END
                    )
                    +
                    (
                        SUM(
                            CASE WHEN DATE(marc.punch_time) = '$fecha'
                                 AND TIME(marc.punch_time) BETWEEN '10:45:59' AND '13:00:00'
                                 THEN CASE WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 10:45:59')) > 1800
                                      THEN 20
                                      ELSE ((CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 10:45:59')) / 300 AS INTEGER) + 1) * 2)
                                 END ELSE 0 END
                        )
                        +
                        CASE WHEN (
                            SELECT COUNT(*) FROM att_punches a2
                            WHERE a2.emp_id = marc.emp_id
                              AND DATE(a2.punch_time) = '$fecha'
                              AND TIME(a2.punch_time) BETWEEN '10:20:00' AND '13:00:00'
                        ) = 0 THEN 40 ELSE 0 END
                    )
                    +
                    (
                        SUM(
                            CASE WHEN DATE(marc.punch_time) = '$fecha'
                                 AND TIME(marc.punch_time) BETWEEN '14:45:59' AND '18:00:00'
                                 THEN CASE WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 14:45:59')) > 1800
                                      THEN 20
                                      ELSE ((CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 14:45:59')) / 300 AS INTEGER) + 1) * 2)
                                 END ELSE 0 END
                        )
                        +
                        CASE WHEN (
                            SELECT COUNT(*) FROM att_punches a2
                            WHERE a2.emp_id = marc.emp_id
                              AND DATE(a2.punch_time) = '$fecha'
                              AND TIME(a2.punch_time) BETWEEN '14:20:59' AND '18:00:00'
                        ) = 0 THEN 40 ELSE 0 END
                    )
                ) AS \"$alias\"";
            }


            $columnsSql[] = $col;
            // Para la suma total se usa el alias seguro, no la fecha original
            $sumColumns[] = "COALESCE(\"$alias\", 0)";
        }

        // Paso 3: Construir las columnas dinámicas separadas por coma
        $columnsSqlStr = implode(", ", $columnsSql);

        // Paso 4: Construir la parte de la suma total de multas
        $totalSql = "";
        if (count($sumColumns) > 0) {
            // Generamos la suma de cada columna usando COALESCE para que si es NULL se convierta en 0.
            $totalSql = ", ( " . implode(" + ", array_map(fn($col) => "COALESCE($col, 0)", $sumColumns)) . " ) AS Total_Multas";
        }

        // Paso 5: Armar la consulta SQL final de forma dinámica
        $sql = "
            WITH primeras_marcaciones_viernes AS (
                SELECT emp_id, MIN(punch_time) AS punch_time
                FROM att_punches
                WHERE strftime('%w', punch_time) = '5'
                AND TIME(punch_time) >= '19:30:59'
                GROUP BY emp_id, DATE(punch_time)
            ),
            multas_calculadas AS (
                SELECT
                    m.emp_firstname,
                    m.emp_lastname,
                    d.dept_name" .
                    (!empty($columnsSqlStr) ? ", $columnsSqlStr" : "") .
                    (!empty($totalSql) ? " $totalSql" : "") . "
                FROM hr_employee AS m
                INNER JOIN att_punches AS marc
                    ON m.id = marc.emp_id
                    AND marc.punch_time BETWEEN ? AND ?
                INNER JOIN hr_department AS d
                    ON m.emp_dept = d.id
                WHERE d.id = ?
                GROUP BY m.id, m.emp_firstname, m.emp_lastname, d.dept_name
            )
            SELECT *,
                (" . (count($sumColumns) > 0 ? implode(" + ", $sumColumns) : "0") . ") AS Total_Multas
            FROM multas_calculadas
            ORDER BY emp_firstname ASC;
        ";


        // Paso 6: Ejecutar la consulta con los parámetros necesarios
        $multas_detalle = DB::connection('sqlite')->select($sql, [$startDate, $endDate, $deptId]);

        $pageTitle = 'Reporte de multas ('
            . Carbon::parse($startDate)->translatedFormat('d M') // Solo día y mes
            . ' - '
            . Carbon::parse($endDate)->translatedFormat('d M')
            . ')';
         // Construir el nombre del archivo con las fechas de inicio y fin
        $fileName = 'reporte_multas_' . Carbon::parse($startDate)->format('Y-m-d') . '_a_' . Carbon::parse($endDate)->format('Y-m-d') . '.xlsx';

        // Exportar los resultados a Excel con el nombre dinámico del archivo
        return Excel::download(new MultasExport($multas_detalle, $dates, $pageTitle), $fileName);
    }

    public function multa(Request $request)
    {
        //dd($request->all());
        // Obtener el rango de fechas enviado desde el formulario
        $dateRange = $request->input('date_range', now()->startOfMonth()->format('d-m-Y 00:00:00') . ' - ' . now()->endOfMonth()->format('d-m-Y 23:59:59'));

        // Separar fecha de inicio y fin
        [$startDate, $endDate] = explode(' - ', $dateRange);

        // Guardar el rango de fechas en la sesión
        $request->session()->put('date_range', $dateRange);

        // Convertir a formato correcto para la base de datos (YYYY-MM-DD)
        $startDate = Carbon::createFromFormat('d-m-Y H:i:s', trim($startDate))->format('Y-m-d H:i:s');
        $endDate = Carbon::createFromFormat('d-m-Y H:i:s', trim($endDate))->format('Y-m-d H:i:s');
        $deptId = $request->input('ministerio_id', 3);

        //dd(['startDate' => $startDate, 'endDate' => $endDate, 'deptId' => $deptId]);

        $pageTitle = 'Reporte de multas, desde el: '
            . Carbon::parse($startDate)->translatedFormat('d F Y')
            . ' hasta el: '
            . Carbon::parse($endDate)->translatedFormat('d F Y');

        // Consulta a la tabla hr_employee en la conexión SQLite
        $ministerios = DB::connection('sqlite')->table('hr_department')->get();


        $act_eventuales = Horario::where('tipo', 0)
            ->whereBetween('fecha', [$startDate, $endDate])
            // ->where('ministerio_id', $deptId)
            ->get()->keyBy('fecha');

        $horario_fijo_jueves = Horario::where('tipo', 1)
            ->select('dia_semana', 'hora_registro', 'hora_multa')
            ->where('dia_semana', 4)
            // ->where('ministerio_id', 1)
            ->get();

        // Consulta detalles con parámetros dinámicos
        $multas_detalle = DB::connection('sqlite')->select("
            -- ✅ Subconsulta: Obtiene la primera marcación de cada empleado los viernes después de las 19:30
            WITH primeras_marcaciones_viernes AS (
                SELECT emp_id, MIN(punch_time) AS punch_time
                FROM att_punches
                WHERE strftime('%w', punch_time) = '5' AND TIME(punch_time) >= '19:30:59'
                GROUP BY emp_id, DATE(punch_time)
            )
            SELECT
                -- ✅ Datos del empleado
                m.emp_firstname,
                m.emp_lastname,

                -- ✅ Fecha y hora de la marcación
                DATE(marc.punch_time) AS punch_date,
                TIME(marc.punch_time) AS punch_hour,

                -- ✅ Departamento del empleado
                d.dept_name,

                -- ✅ Convertimos el número del día en el nombre del día correspondiente
                CASE strftime('%w', marc.punch_time)
                    WHEN '0' THEN 'Domingo'
                    WHEN '1' THEN 'Lunes'
                    WHEN '2' THEN 'Martes'
                    WHEN '3' THEN 'Miércoles'
                    WHEN '4' THEN 'Jueves'
                    WHEN '5' THEN 'Viernes'
                    WHEN '6' THEN 'Sábado'
                END AS dia_semana,

                -- ✅ Cálculo de multas según el día y la hora de marcación
                CASE
                    -- ✅ Excepción para el 21 de febrero de 2025: calcular multa por cada 5 minutos de retraso hasta un máximo de 20 Bs
                    WHEN DATE(marc.punch_time) = '2025-02-21' AND strftime('%w', marc.punch_time) = '5' THEN
                        CASE
                            WHEN TIME(marc.punch_time) > '22:15:59' THEN
                                CASE
                                    -- ✅ Si se pasa de 30 minutos, la multa es de 20 Bs
                                    WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 22:15:59')) > 1800
                                    THEN '20'
                                    -- ✅ Si no, calculamos la multa en incrementos de 2 Bs cada 5 minutos
                                    ELSE (CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 22:15:59')) / 300 AS INTEGER) + 1) * 2
                                END
                            ELSE '0' -- ✅ Si llega antes de las 22:15:59, no hay multa
                        END

                    -- ✅ Reglas generales para los viernes
                    WHEN strftime('%w', marc.punch_time) = '5'
                        AND NOT EXISTS (
                            SELECT 1
                            FROM primeras_marcaciones_viernes p
                            WHERE p.emp_id = marc.emp_id
                            AND p.punch_time = marc.punch_time
                        )
                    THEN '0' -- ✅ Si no es su primera marcación del viernes después de 19:30, no hay multa

                    -- ✅ Para otros viernes distintos al 21 de febrero, si marca después de 19:30:59, debe producto
                    WHEN strftime('%w', marc.punch_time) = '5' AND TIME(marc.punch_time) > '19:30:59' AND DATE(marc.punch_time) != '2025-02-21' THEN 'Debe producto'

                    -- ✅ Lógica para los otros días como Jueves y Domingo sigue igual...

                    -- ✅ Multa para los jueves después de las 19:15
                    WHEN strftime('%w', marc.punch_time) = '4' AND TIME(marc.punch_time) > '19:15:59'
                    THEN
                        CASE
                            WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 19:15:59')) > 1800
                            THEN '20'
                            ELSE (CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 19:15:59')) / 300 AS INTEGER) + 1) * 2
                        END

                    -- ✅ Multas para el domingo (día 0) con la lógica de 3 marcaciones
                    WHEN strftime('%w', marc.punch_time) = '0' THEN
                        CASE
                            WHEN TIME(marc.punch_time) > '07:45:59' AND TIME(marc.punch_time) <= '10:00:00'
                            THEN
                                CASE
                                    WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 07:45:59')) > 1800
                                    THEN '20'
                                    ELSE (CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 07:45:59')) / 300 AS INTEGER) + 1) * 2
                                END
                            WHEN TIME(marc.punch_time) > '10:45:59' AND TIME(marc.punch_time) <= '13:00:00'
                            THEN
                                CASE
                                    WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 10:45:59')) > 1800
                                    THEN '20'
                                    ELSE (CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 10:45:59')) / 300 AS INTEGER) + 1) * 2
                                END
                            WHEN TIME(marc.punch_time) > '14:45:59' AND TIME(marc.punch_time) <= '18:00:00'
                            THEN
                                CASE
                                    WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 14:45:59')) > 1800
                                    THEN '20'
                                    ELSE (CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 14:45:59')) / 300 AS INTEGER) + 1) * 2
                                END
                            ELSE '0'
                        END
                    ELSE '0'
                END AS multa_bs
            FROM hr_employee AS m
            INNER JOIN att_punches AS marc ON m.id = marc.emp_id
            INNER JOIN hr_department AS d ON m.emp_dept = d.id
            WHERE marc.punch_time BETWEEN ? AND ?
            AND d.id = ?
            ORDER BY marc.punch_time ASC;
        ", [$startDate, $endDate, $deptId]);




        // Paso 1: Obtener las fechas únicas dentro del rango para los días de interés (jueves=4, viernes=5, domingo=0)
        $datesResult = DB::connection('sqlite')->select("
            SELECT DISTINCT DATE(punch_time) AS fecha, strftime('%w', punch_time) AS dia_semana
            FROM att_punches
            WHERE punch_time BETWEEN ? AND ?
            AND strftime('%w', punch_time) IN ('0', '4', '5')
            ORDER BY fecha ASC
        ", [$startDate, $endDate]);

        $dayNames = [
            '0' => 'Domingo',
            '1' => 'Lunes',
            '2' => 'Martes',
            '3' => 'Miércoles',
            '4' => 'Jueves',
            '5' => 'Viernes',
            '6' => 'Sábado'
        ];

        $dates = [];
        foreach ($datesResult as $row) {
            $dates[] = [
                'fecha'      => $row->fecha,
                'alias'      => 'd_' . str_replace('-', '_', $row->fecha),
                'dia_semana' => $row->dia_semana,
                'dia_semana_lit' => $dayNames[$row->dia_semana]
            ];
        }

        // Paso 2: Construir dinámicamente las columnas del SELECT para cada fecha
        $columnsSql = [];
        $sumColumns = []; // Para construir la suma total de multas

        foreach ($dates as $d) {
            $fecha = $d['fecha'];
            $alias = $d['alias'];
            $dia   = $d['dia_semana']; // '0' (Dom), '4' (Jue), '5' (Vie)

            if ($dia == '5') {
                // Para viernes: se contempla la excepción del 21 de febrero y la lógica general.
                $col = "SUM(
                    CASE
                        WHEN DATE(marc.punch_time) = '$fecha' THEN (
                            CASE
                                WHEN '$fecha' = '2025-02-21' THEN
                                    CASE
                                        WHEN TIME(marc.punch_time) > '22:15:59' THEN
                                            CASE
                                                WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 22:15:59')) > 1800
                                                    THEN 20  -- Multa de 20 Bs si el retraso es mayor a 30 minutos
                                                    ELSE ((CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 22:15:59')) / 300 AS INTEGER) + 1) * 2)  -- Multa progresiva de 2 Bs por cada 5 minutos de retraso
                                            END
                                        ELSE 0  -- No se aplica multa si llega antes de las 22:15:59 en el 21 de febrero
                                    END
                                ELSE
                                    CASE
                                        WHEN TIME(marc.punch_time) > '19:30:59' THEN
                                            0 -- 'ACA DEBERIA PRODUCTO'
                                        ELSE
                                            0
                                    END
                            END
                        ) ELSE 0
                    END
                ) AS \"$alias\"";
            } elseif ($dia == '4') {
                // Para jueves:
                // - Si hay marcación y es después de las 19:15:59 se calcula la multa según la lógica original.
                // - Además, se agrega una penalización de 40 Bs si NO hay ninguna marcación en el rango de 17:00:00 a 21:00:00.
                $col = "(
                    SUM(
                        CASE WHEN DATE(marc.punch_time) = '$fecha' THEN (
                            CASE
                                WHEN TIME(marc.punch_time) > '19:15:59' THEN
                                    CASE WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 19:15:59')) > 1800
                                        THEN 20
                                        ELSE ((CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 19:15:59')) / 300 AS INTEGER) + 1) * 2)
                                    END
                                ELSE 0
                            END
                        ) ELSE 0 END
                    )
                    +
                    CASE WHEN (
                        SELECT COUNT(*) FROM att_punches a2
                        WHERE a2.emp_id = marc.emp_id
                          AND DATE(a2.punch_time) = '$fecha'
                          AND TIME(a2.punch_time) BETWEEN '16:00:00' AND '21:00:00'
                    ) = 0 THEN 40 ELSE 0 END
                ) AS \"$alias\"";
            }
            elseif ($dia == '0') {
                $col = "(
                    (
                        SUM(
                            CASE WHEN DATE(marc.punch_time) = '$fecha'
                                 AND TIME(marc.punch_time) BETWEEN '07:45:59' AND '10:00:00'
                                 THEN CASE WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 07:45:59')) > 1800
                                      THEN 20
                                      ELSE ((CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 07:45:59')) / 300 AS INTEGER) + 1) * 2)
                                 END ELSE 0 END
                        )
                        +
                        CASE WHEN (
                            SELECT COUNT(*) FROM att_punches a2
                            WHERE a2.emp_id = marc.emp_id
                              AND DATE(a2.punch_time) = '$fecha'
                              AND TIME(a2.punch_time) BETWEEN '07:00:00' AND '10:00:00'
                        ) = 0 THEN 40 ELSE 0 END
                    )
                    +
                    (
                        SUM(
                            CASE WHEN DATE(marc.punch_time) = '$fecha'
                                 AND TIME(marc.punch_time) BETWEEN '10:45:59' AND '13:00:00'
                                 THEN CASE WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 10:45:59')) > 1800
                                      THEN 20
                                      ELSE ((CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 10:45:59')) / 300 AS INTEGER) + 1) * 2)
                                 END ELSE 0 END
                        )
                        +
                        CASE WHEN (
                            SELECT COUNT(*) FROM att_punches a2
                            WHERE a2.emp_id = marc.emp_id
                              AND DATE(a2.punch_time) = '$fecha'
                              AND TIME(a2.punch_time) BETWEEN '10:20:00' AND '13:00:00'
                        ) = 0 THEN 40 ELSE 0 END
                    )
                    +
                    (
                        SUM(
                            CASE WHEN DATE(marc.punch_time) = '$fecha'
                                 AND TIME(marc.punch_time) BETWEEN '14:45:59' AND '18:00:00'
                                 THEN CASE WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 14:45:59')) > 1800
                                      THEN 20
                                      ELSE ((CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 14:45:59')) / 300 AS INTEGER) + 1) * 2)
                                 END ELSE 0 END
                        )
                        +
                        CASE WHEN (
                            SELECT COUNT(*) FROM att_punches a2
                            WHERE a2.emp_id = marc.emp_id
                              AND DATE(a2.punch_time) = '$fecha'
                              AND TIME(a2.punch_time) BETWEEN '14:20:59' AND '18:00:00'
                        ) = 0 THEN 40 ELSE 0 END
                    )
                ) AS \"$alias\"";
            }


            $columnsSql[] = $col;
            // Para la suma total se usa el alias seguro, no la fecha original
            $sumColumns[] = "COALESCE(\"$alias\", 0)";
        }

        // Paso 3: Construir las columnas dinámicas separadas por coma
        $columnsSqlStr = implode(", ", $columnsSql);

        // Paso 4: Construir la parte de la suma total de multas
        $totalSql = "";
        if (count($sumColumns) > 0) {
            // Generamos la suma de cada columna usando COALESCE para que si es NULL se convierta en 0.
            $totalSql = ", ( " . implode(" + ", array_map(fn($col) => "COALESCE($col, 0)", $sumColumns)) . " ) AS Total_Multas";
        }

        // Paso 5: Armar la consulta SQL final de forma dinámica
        $sql = "
            WITH primeras_marcaciones_viernes AS (
                SELECT emp_id, MIN(punch_time) AS punch_time
                FROM att_punches
                WHERE strftime('%w', punch_time) = '5'
                AND TIME(punch_time) >= '19:30:59'
                GROUP BY emp_id, DATE(punch_time)
            ),
            multas_calculadas AS (
                SELECT
                    m.emp_firstname,
                    m.emp_lastname,
                    d.dept_name" .
                    (!empty($columnsSqlStr) ? ", $columnsSqlStr" : "") .
                    (!empty($totalSql) ? " $totalSql" : "") . "
                FROM hr_employee AS m
                INNER JOIN att_punches AS marc
                    ON m.id = marc.emp_id
                    AND marc.punch_time BETWEEN ? AND ?
                INNER JOIN hr_department AS d
                    ON m.emp_dept = d.id
                WHERE d.id = ?
                GROUP BY m.id, m.emp_firstname, m.emp_lastname, d.dept_name
            )
            SELECT *,
                (" . (count($sumColumns) > 0 ? implode(" + ", $sumColumns) : "0") . ") AS Total_Multas
            FROM multas_calculadas
            ORDER BY emp_firstname ASC;
        ";


        // Paso 6: Ejecutar la consulta con los parámetros necesarios
        $multas_detalle_reporte = DB::connection('sqlite')->select($sql, [$startDate, $endDate, $deptId]);


        // Consulta general con parámetros dinámicos
        $multas_general = DB::connection('sqlite')->select("
            WITH primeras_marcaciones_viernes AS (
                SELECT emp_id, MIN(punch_time) AS punch_time
                FROM att_punches
                WHERE strftime('%w', punch_time) = '5' AND TIME(punch_time) >= '19:30:00'
                GROUP BY emp_id, DATE(punch_time)
            ),

            multas_por_marcacion AS (
                SELECT
                    m.id AS emp_id,
                    m.emp_firstname,
                    m.emp_lastname,
                    DATE(marc.punch_time) AS punch_date,
                    d.dept_name,

                    CASE
                        -- ✅ Excepción para el 21 de febrero de 2025: calcular multa por cada 5 minutos de retraso hasta un máximo de 20 Bs
                        WHEN DATE(marc.punch_time) = '2025-02-21' AND strftime('%w', marc.punch_time) = '5' THEN
                            CASE
                                WHEN TIME(marc.punch_time) > '22:15:59' THEN
                                    CASE
                                        -- ✅ Si se pasa de 30 minutos, la multa es de 20 Bs
                                        WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 22:15:59')) > 1800
                                        THEN '20'
                                        -- ✅ Si no, calculamos la multa en incrementos de 2 Bs cada 5 minutos
                                        ELSE (CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 22:15:59')) / 300 AS INTEGER) + 1) * 2
                                    END
                                ELSE '0' -- ✅ Si llega antes de las 22:15:59, no hay multa
                            END

                        -- ✅ Reglas generales para los viernes
                        WHEN strftime('%w', marc.punch_time) = '5'
                            AND NOT EXISTS (
                                SELECT 1
                                FROM primeras_marcaciones_viernes p
                                WHERE p.emp_id = marc.emp_id
                                AND p.punch_time = marc.punch_time
                            )
                        THEN '0' -- ✅ Si no es su primera marcación del viernes después de 19:30, no hay multa

                        -- ✅ Para otros viernes distintos al 21 de febrero, si marca después de 19:30:59, debe producto
                        WHEN strftime('%w', marc.punch_time) = '5' AND TIME(marc.punch_time) > '19:30:59' AND DATE(marc.punch_time) != '2025-02-21' THEN 'Debe producto'

                        -- Multa para los jueves después de las 19:15:59
                        WHEN strftime('%w', marc.punch_time) = '4' AND TIME(marc.punch_time) > '19:15:59'
                        THEN
                            CASE
                                WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 19:15:59')) > 1800
                                THEN '20'
                                ELSE (CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 19:15:59')) / 300 AS INTEGER) + 1) * 2
                            END

                        -- Multas para el domingo (día 0)
                        WHEN strftime('%w', marc.punch_time) = '0' THEN
                            CASE
                                -- 1ra marcación (07:45:59 a 10:00)
                                WHEN TIME(marc.punch_time) > '07:45:59' AND TIME(marc.punch_time) <= '10:00:59'
                                THEN
                                    CASE
                                        WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 07:45:59')) > 1800
                                        THEN '20'
                                        ELSE (CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 07:45:59')) / 300 AS INTEGER) + 1) * 2
                                    END

                                -- 2da marcación (10:45:59 a 13:00)
                                WHEN TIME(marc.punch_time) > '10:45:00' AND TIME(marc.punch_time) <= '13:00:59'
                                THEN
                                    CASE
                                        WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 10:45:59')) > 1800
                                        THEN '20'
                                        ELSE (CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 10:45:59')) / 300 AS INTEGER) + 1) * 2
                                    END

                                -- 3ra marcación (14:45:59 a 17:00)
                                WHEN TIME(marc.punch_time) > '14:45:59' AND TIME(marc.punch_time) <= '18:00:59'
                                THEN
                                    CASE
                                        WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 14:45:59')) > 1800
                                        THEN '20'
                                        ELSE (CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 14:45:59')) / 300 AS INTEGER) + 1) * 2
                                    END
                                ELSE '0'
                            END
                        ELSE '0'
                    END AS multa_bs,
            CASE
                -- Excluir 21 de febrero de 2025 de productos
                WHEN DATE(marc.punch_time) = '2025-02-21' AND strftime('%w', marc.punch_time) = '5' THEN 0

                -- Si es viernes y después de 19:30, considerar producto SOLO si es la primera marcación del viernes
                WHEN strftime('%w', marc.punch_time) = '5' AND TIME(marc.punch_time) > '19:30:59'
                AND NOT EXISTS (
                    SELECT 1
                    FROM primeras_marcaciones_viernes p
                    WHERE p.emp_id = marc.emp_id
                    AND p.punch_time = marc.punch_time
                ) THEN 0 -- Si no es la primera marcación, no considerar producto

                -- Si es viernes y después de 19:30, es la primera marcación, se debe considerar producto
                WHEN strftime('%w', marc.punch_time) = '5' AND TIME(marc.punch_time) > '19:30:59' THEN 1

                ELSE 0
            END AS producto
                FROM hr_employee AS m
                INNER JOIN att_punches AS marc ON m.id = marc.emp_id
                INNER JOIN hr_department AS d ON m.emp_dept = d.id
                WHERE marc.punch_time BETWEEN ? AND ?
                AND d.id = ?
                ORDER BY marc.punch_time
            )

            SELECT
                emp_id,
                emp_firstname,
                emp_lastname,
                dept_name,
                SUM(CASE WHEN multa_bs = 'Debe producto' THEN 0 ELSE CAST(multa_bs AS INTEGER) END) AS total_multa_bs,
                SUM(producto) AS productos_adeudados
            FROM multas_por_marcacion
            GROUP BY emp_id, emp_firstname, emp_lastname, dept_name
            ORDER BY emp_firstname, emp_lastname;


        ", [$startDate, $endDate, $deptId]);

        return view('admin.reportes.multa', compact('ministerios', 'multas_detalle', 'multas_detalle_reporte', 'multas_general', 'pageTitle', 'deptId', 'dateRange', 'dates'));
    }

    public function asistencia(Request $request)
    {
        $pageTitle = 'Reporte de asistencia';
        return view('admin.reportes.asistencia', compact('pageTitle'));
    }

    public function fidelizacion(Request $request)
    {
        $pageTitle = 'Reporte de fidelización';
        return view('admin.reportes.fidelizacion', compact('pageTitle'));
    }
}
