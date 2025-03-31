<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use Carbon\Carbon;
use App\Models\Horario;
use App\Models\Ministerio;
use Illuminate\Http\Request;
use App\Exports\MultasExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Asistencia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ReporteController extends Controller
{
    public function exportarReporte(Request $request)
    {
        // Recuperar el rango de fechas desde la sesión o el request
        $dateRange = $request->session()->get('date_range', now()->startOfMonth()->format('d-m-Y 00:00:00') . ' - ' . now()->format('d-m-Y 23:59:59'));

        [$startDate, $endDate] = explode(' - ', $dateRange);

        // Convertir las fechas a formato adecuado
        $startDate = Carbon::createFromFormat('d-m-Y H:i:s', trim($startDate))->format('Y-m-d H:i:s');
        $endDate = Carbon::createFromFormat('d-m-Y H:i:s', trim($endDate))->format('Y-m-d H:i:s');
        $ministerioId = $request->input('ministerio_propio_id', 1);

        // $reporteDinamico = $this->generarReporteColumnasDinamicas($deptId, $startDate, $endDate);
        $multas_detalle = $this->generarReporteColumnasDinamicas($ministerioId, $startDate, $endDate);
        //dd($reporteDinamico);

        // Obtener los horarios por fecha
        $horariosPorFecha = $this->obtenerHorariosPorMinisterio($ministerioId, $startDate, $endDate);

        // Usar una función separada para generar la cabecera con las fechas y actividades
        $dates = $this->obtenerCabeceraFechas($horariosPorFecha);
        //dd($cabeceraFechas);

        $pageTitle = 'Reporte de multas ('
            . Carbon::parse($startDate)->translatedFormat('d M')
            . ' - '
            . Carbon::parse($endDate)->translatedFormat('d M')
            . ')';

        $fileName = 'reporte_multas_' . Carbon::parse($startDate)->format('Y-m-d') . '_a_' . Carbon::parse($endDate)->format('Y-m-d') . '.xlsx';

        // Exportar los resultados a Excel con el nombre dinámico del archivo
        return Excel::download(new MultasExport($multas_detalle, $dates, $pageTitle), $fileName);
    }

    public function multa(Request $request)
    {

        //dd($request->all());
        // Obtener el rango de fechas enviado desde el formulario
        $dateRange = $request->input('date_range', now()->startOfMonth()->format('d-m-Y 00:00:00') . ' - ' . now()->format('d-m-Y 23:59:59'));

        // Separar fecha de inicio y fin
        [$startDate, $endDate] = explode(' - ', $dateRange);

        // Guardar el rango de fechas en la sesión
        $request->session()->put('date_range', $dateRange);

        // Convertir a formato correcto para la base de datos (YYYY-MM-DD)
        $startDate = Carbon::createFromFormat('d-m-Y H:i:s', trim($startDate))->format('Y-m-d H:i:s');
        $endDate = Carbon::createFromFormat('d-m-Y H:i:s', trim($endDate))->format('Y-m-d H:i:s');
        $deptId = $request->input('ministerio_id', 3);
        $ministerioId = $request->input('ministerio_propio_id', 1);

        //dd(['startDate' => $startDate, 'endDate' => $endDate, 'deptId' => $deptId]);

        $pageTitle = 'Reporte de multas, desde el: '
            . Carbon::parse($startDate)->translatedFormat('d F Y')
            . ' hasta el: '
            . Carbon::parse($endDate)->translatedFormat('d F Y');






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

        //DD($datesResult);

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
                'fecha' => $row->fecha,
                'alias' => 'd_' . str_replace('-', '_', $row->fecha),
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
            $dia = $d['dia_semana']; // '0' (Dom), '4' (Jue), '5' (Vie)

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
            } elseif ($dia == '0') {
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
        //dd($multas_detalle_reporte);

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



        // Consulta a la tabla hr_employee en la conexión SQLite
        $ministerios = DB::connection('sqlite')->table('hr_department')->get();


        $ministeriosPropios = Ministerio::where('estado', Status::ACTIVE)->get();
        // $reporteDinamico = $this->generarReporteColumnasDinamicas($deptId, $startDate, $endDate);
        $reporteDinamico = $this->generarReporteColumnasDinamicas($ministerioId, $startDate, $endDate);
        //dd($reporteDinamico);

        // Obtener los horarios por fecha
        $horariosPorFecha = $this->obtenerHorariosPorMinisterio($ministerioId, $startDate, $endDate);

        // Usar una función separada para generar la cabecera con las fechas y actividades
        $cabeceraFechas = $this->obtenerCabeceraFechas($horariosPorFecha);
        //dd($cabeceraFechas);

        return view('admin.reportes.multa', compact('ministerios', 'ministeriosPropios', 'multas_detalle', 'multas_detalle_reporte', 'multas_general', 'pageTitle', 'ministerioId', 'deptId', 'dateRange', 'dates', 'reporteDinamico', 'cabeceraFechas'));
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

    /**
     * Obtiene los horarios (fijos y eventuales) para el ministerio dentro de un rango de fechas.
     */
    public function obtenerHorariosPorMinisterio($ministerioId, $startDate, $endDate)
    {
        $dayMapping = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

        // Obtener todos los horarios activos del ministerio con su actividadServicio
        $horarios = Ministerio::findOrFail($ministerioId)
            ->horarios()
            ->where('estado', Status::ACTIVE)
            ->with('actividadServicio:id,nombre')
            ->get();

        // Obtener las excepciones activas asociadas al ministerio dentro del rango
        $excepciones = \App\Models\Excepcion::where('estado', true)
            ->whereHas('ministerios', function ($q) use ($ministerioId) {
                $q->where('ministerio_id', $ministerioId);
            })
            ->where(function ($query) use ($startDate, $endDate) {
                // Se incluyen las excepciones que inician o terminan en el rango, o que abarcan el inicio
                $query->whereBetween('fecha', [Carbon::parse($startDate)->format('Y-m-d'), Carbon::parse($endDate)->format('Y-m-d')])
                    ->orWhere(function ($q) use ($startDate) {
                        $q->where('fecha', '<=', Carbon::parse($startDate)->format('Y-m-d'))
                            ->where('hasta', '>=', Carbon::parse($startDate)->format('Y-m-d'));
                    });
            })
            ->get();

        $resultados = [];
        $fechaActual = Carbon::parse($startDate);
        $fechaFin = Carbon::parse($endDate);

        while ($fechaActual <= $fechaFin) {
            $diaIndice = $fechaActual->format('w'); // 0 = Domingo, 1 = Lunes, etc.
            $fechaString = $fechaActual->format('Y-m-d');

            // Filtrar las excepciones que afectan el día actual
            $excepcionesHoy = $excepciones->filter(function ($excepcion) use ($fechaString) {
                $fechaInicio = Carbon::parse($excepcion->fecha)->format('Y-m-d');
                $fechaFinEx = $excepcion->hasta ? Carbon::parse($excepcion->hasta)->format('Y-m-d') : $fechaInicio;
                return $fechaString >= $fechaInicio && $fechaString <= $fechaFinEx;
            });

            // Verificar si para el día actual hay alguna excepción de día completo o de varios días
            $excepcionDiaCompleto = $excepcionesHoy->first(function ($ex) {
                return in_array($ex->dia_entero, [1, 2]);
            });

            // Para los horarios fijos, si existe una excepción de día completo o de varios días se omite el día
            if (!$excepcionDiaCompleto) {
                // Obtener los horarios fijos para el día de la semana
                $horariosFijos = $horarios->where('tipo', 1)->where('dia_semana', $diaIndice);

                if ($horariosFijos->isNotEmpty()) {
                    // Si existe una excepción parcial (rango de horas) se filtran las actividades afectadas
                    $actividadesFiltradas = $horariosFijos->map(function ($h) use ($excepcionesHoy) {
                        $excepcionParcial = $excepcionesHoy->first(function ($ex) {
                            return $ex->dia_entero == 0 && $ex->hora_inicio && $ex->hora_fin;
                        });

                        if ($excepcionParcial) {
                            $horaRegistro = Carbon::parse($h->hora_registro);
                            $horaInicioEx = Carbon::parse($excepcionParcial->hora_inicio);
                            $horaFinEx = Carbon::parse($excepcionParcial->hora_fin);
                            // Si la hora de registro se encuentra dentro del rango de la excepción parcial, se descarta
                            if ($horaRegistro->between($horaInicioEx, $horaFinEx, true)) {
                                return null;
                            }
                        }
                        return $h;
                    })->filter(); // se remueven los null

                    if ($actividadesFiltradas->isNotEmpty()) {
                        $resultados[$fechaString] = (object)[
                            'fecha'       => $fechaString,
                            'dia_semana'  => $dayMapping[$diaIndice],
                            'actividades' => $actividadesFiltradas->map(function ($h) {
                                return (object)[
                                    'tipo'              => 'fijo',
                                    'nombre_actividad'  => $h->actividadServicio->nombre ?? null,
                                    'hora_registro'     => $h->hora_registro,
                                    'hora_multa'        => $h->hora_multa,
                                    'hora_limite'       => $h->hora_limite
                                ];
                            })->values()->toArray()
                        ];
                    }
                }
            }
            // Si hay excepción de día completo, no se agrega ningún horario fijo para esa fecha

            $fechaActual->modify('+1 day');
        }

        // Se procesan los horarios eventuales sin filtrar excepciones (se pueden deshabilitar manualmente)
        $horariosEventuales = $horarios->where('tipo', 0)
            ->whereNotNull('fecha')
            ->filter(function ($horario) use ($startDate, $endDate) {
                return Carbon::parse($horario->fecha)->between(
                    Carbon::parse($startDate)->format('Y-m-d'),
                    Carbon::parse($endDate)->format('Y-m-d'),
                    true
                );
            });

        foreach ($horariosEventuales as $horario) {
            $fechaString = Carbon::parse($horario->fecha)->format('Y-m-d');
            $diaIndice = Carbon::parse($horario->fecha)->format('w');

            if (!isset($resultados[$fechaString])) {
                $resultados[$fechaString] = (object)[
                    'fecha'       => $fechaString,
                    'dia_semana'  => $dayMapping[$diaIndice] ?? null,
                    'actividades' => []
                ];
            }
            $resultados[$fechaString]->actividades[] = (object)[
                'tipo'              => 'eventual',
                'nombre_actividad'  => $horario->actividadServicio->nombre ?? null,
                'hora_registro'     => $horario->hora_registro,
                'hora_multa'        => $horario->hora_multa,
                'hora_limite'       => $horario->hora_limite
            ];
        }

        Log::info("Horarios obtenidos para ministerio {$ministerioId}", [
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'resultados' => $resultados
        ]);

        // Se retorna un array con los valores
        return array_values($resultados);
    }


    /**
     * Calcula la multa para una asistencia, basándose en el horario (o regla por defecto) y la regla de multa.
     * Si la asistencia es nula, se aplica la multa por falta.
     */
    protected function calcularMulta($asistencia, $horario, $reglaMulta)
    {
        Log::debug("Iniciando cálculo de multa", [
            'asistencia' => $asistencia,
            'horario'    => $horario,
            'reglaMulta' => $reglaMulta
        ]);

        // Si no se encontró asistencia, se asume que el usuario no marcó dentro del periodo permitido.
        if (!$asistencia) {
            Log::info("No se encontró asistencia. Aplicando multa por falta.", [
                'multa_por_falta' => $reglaMulta->multa_por_falta
            ]);
            return $reglaMulta->multa_por_falta;
        }

        // Se extraen las horas desde la tabla de horarios.
        $horaRegistro   = Carbon::parse($horario->hora_registro);
        $horaMulta     = Carbon::parse($horario->hora_multa);
        $horaLimite    = Carbon::parse($horario->hora_limite);
        $horaMarcacion = Carbon::parse($asistencia->hora_marcacion);

        Log::debug("Horas de referencia", [
            'hora_registro'  => $horaRegistro->format('H:i:s'),
            'hora_multa'     => $horaMulta->format('H:i:s'),
            'hora_limite'    => $horaLimite->format('H:i:s'),
            'hora_marcacion' => $horaMarcacion->format('H:i:s')
        ]);

        // 1. Si el usuario marca antes o justo en la hora_registro, se considera puntual.
        if ($horaMarcacion->lessThanOrEqualTo($horaRegistro)) {
            Log::info("Marcación a tiempo (antes o en hora_registro). No se aplica multa.", [
                'hora_marcacion' => $horaMarcacion->format('H:i:s'),
                'hora_registro'  => $horaRegistro->format('H:i:s')
            ]);
            return 0;
        }

        // 2. Si el usuario marca entre hora_registro y hora_multa, se considera que aún no corre la multa.
        if ($horaMarcacion->lessThanOrEqualTo($horaMulta)) {
            Log::info("Marcación realizada antes de la hora_multa. No se aplica multa.", [
                'hora_marcacion' => $horaMarcacion->format('H:i:s'),
                'hora_multa'     => $horaMulta->format('H:i:s')
            ]);
            return 0;
        }

        // 3. Si la marcación es posterior a hora_multa, se calcula el retraso.
        if ($horaMarcacion->greaterThan($horaLimite)) {
            Log::info("Marcación posterior a la hora_limite. Se usará la diferencia máxima.", [
                'hora_limite' => $horaLimite->format('H:i:s'),
                'hora_multa'  => $horaMulta->format('H:i:s')
            ]);
            $retraso = $horaLimite->diffInMinutes($horaMulta);
        } else {
            $retraso = $horaMarcacion->diffInMinutes($horaMulta);
        }

        Log::debug("Retraso calculado a partir de hora_multa", ['retraso_minutos' => $retraso]);

        // 4. Si el retraso supera o iguala el umbral definido para retraso largo, se aplica la multa fija.
        if ($retraso >= $reglaMulta->minutos_retraso_largo) {
            Log::info("Retraso largo detectado.", [
                'retraso'               => $retraso,
                'minutos_retraso_largo' => $reglaMulta->minutos_retraso_largo,
                'multa_por_retraso_largo' => $reglaMulta->multa_por_retraso_largo
            ]);
            return $reglaMulta->multa_por_retraso_largo;
        }

        // 5. Para retrasos menores al umbral, se calcula la multa incremental.
        $intervalos = ceil($retraso / $reglaMulta->minutos_por_incremento);
        $multaIncremental = $intervalos * $reglaMulta->multa_incremental;
        Log::info("Calculando multa incremental", [
            'retraso'                => $retraso,
            'minutos_por_incremento' => $reglaMulta->minutos_por_incremento,
            'intervalos'             => $intervalos,
            'multa_incremental'      => $reglaMulta->multa_incremental,
            'multa_calculada'        => $multaIncremental
        ]);

        return $multaIncremental;
    }

    /**
     * Genera el reporte de multas con columnas dinámicas.
     * Cada fila representa un usuario y cada columna una fecha (con alias d_YYYY-MM-DD).
     * En cada fecha se muestra un arreglo con el total del día y el detalle por cada horario.
     */
    public function generarReporteColumnasDinamicas($ministerioId, $startDate, $endDate)
    {
        Log::debug("Generando reporte de multas", [
            'ministerioId' => $ministerioId,
            'startDate'    => $startDate,
            'endDate'      => $endDate
        ]);

        // Obtener horarios agrupados por fecha
        $horariosPorFecha = $this->obtenerHorariosPorMinisterio($ministerioId, $startDate, $endDate);
        Log::debug("Horarios por fecha obtenidos", ['horariosPorFecha' => $horariosPorFecha]);

        // Extraer y ordenar las fechas disponibles
        $fechas = array_unique(array_map(function ($item) {
            return $item->fecha;
        }, $horariosPorFecha));
        sort($fechas);
        Log::debug("Fechas extraídas y ordenadas", ['fechas' => $fechas]);

        // Obtener la regla de multa y los usuarios asociados
        $ministerio = Ministerio::findOrFail($ministerioId);
        $reglaMulta = $ministerio->reglasMultas()->where('estado', Status::ACTIVE)->first();
        if (!$reglaMulta) {
            throw new \Exception("No se encontró una regla de multa para el ministerio.");
        }
        Log::info("Regla de multa obtenida para ministerio", [
            'ministerioId' => $ministerioId,
            'reglaMulta'   => $reglaMulta
        ]);

        $usuarios = $ministerio->usuarios;
        Log::debug("Usuarios del ministerio", ['usuarios' => $usuarios]);

        // Agrupar asistencias por llave compuesta: ci + '_' + fecha (Y-m-d)
        $ciUsuarios = $usuarios->pluck('ci')->toArray();
        $asistenciasQuery = Asistencia::whereIn('ci', $ciUsuarios)
            ->whereBetween('fecha', [$startDate, $endDate])
            ->get();
        Log::debug('Resultado de la consulta de asistencias', [
            'asistenciasQuery' => $asistenciasQuery->toArray()
        ]);

        $asistencias = $asistenciasQuery->groupBy(function ($item) {
            return $item->ci . '_' . Carbon::parse($item->fecha)->format('Y-m-d');
        });
        Log::debug("Asistencias agrupadas", ['asistencias' => $asistencias->toArray()]);

        // Construir el reporte
        $reporte = [];
        foreach ($usuarios as $usuario) {
            $fila = [
                'nombre'       => $usuario->name,
                'apellido'     => $usuario->last_name,
                'ministerio'   => $usuario->ministerios->firstWhere('id', $ministerioId)->nombre ?? 'No asignado',
                'Total_Multas' => 0
            ];

            $totalMultasUsuario = 0;
            // Procesar cada fecha disponible
            foreach ($fechas as $fecha) {
                // Llave para obtener todas las asistencias del usuario en ese día
                $key = $usuario->ci . '_' . $fecha;
                $asistenciasDia = isset($asistencias[$key]) ? $asistencias[$key] : collect();
                Log::debug("Procesando asistencias para usuario", [
                    'usuario_ci'  => $usuario->ci,
                    'fecha'       => $fecha,
                    'key'         => $key,
                    'asistencias' => $asistenciasDia->toArray()
                ]);

                // Obtener todos los horarios para la fecha
                $horariosDia = array_filter($horariosPorFecha, function ($h) use ($fecha) {
                    return $h->fecha === $fecha;
                });

                // Agrupar las actividades de la fecha de forma única, usando el nombre de la actividad
                $actividadesGroup = [];
                foreach ($horariosDia as $horario) {
                    foreach ($horario->actividades as $actividad) {
                        $nombreActividad = $actividad->nombre_actividad ?? 'Sin nombre';
                        // Agrupamos todas las instancias de la misma actividad (por nombre)
                        if (!isset($actividadesGroup[$nombreActividad])) {
                            $actividadesGroup[$nombreActividad] = [];
                        }
                        $actividadesGroup[$nombreActividad][] = $actividad;
                    }
                }

                // Procesar cada actividad agrupada
                foreach ($actividadesGroup as $nombreActividad => $actividadesArray) {
                    $multaActividad = 0;
                    $detalleActividad = [];
                    foreach ($actividadesArray as $actividad) {
                        // Convertir tiempos del horario a Carbon
                        $horaRegistro = Carbon::parse($actividad->hora_registro);
                        $horaLimite   = Carbon::parse($actividad->hora_limite);

                        // Filtrar asistencias que estén en el rango entre hora_registro y hora_limite
                        $asistenciasEnRango = $asistenciasDia->filter(function ($a) use ($horaRegistro, $horaLimite) {
                            $horaMarcacion = Carbon::parse($a->hora_marcacion);
                            return $horaMarcacion->between($horaRegistro, $horaLimite, true);
                        });
                        // Ordenar y obtener la asistencia más temprana
                        $asistenciaActividad = $asistenciasEnRango->sortBy(function ($a) {
                            return Carbon::parse($a->hora_marcacion)->timestamp;
                        })->first();

                        // Calcular la multa para la actividad
                        $multa = $this->calcularMulta($asistenciaActividad, $actividad, $reglaMulta);
                        $multaActividad += $multa;

                        $detalleActividad[] = [
                            'nombre_actividad' => $nombreActividad,
                            'tipo'             => $actividad->tipo,
                            'hora_registro'    => $actividad->hora_registro,
                            'hora_multa'       => $actividad->hora_multa,
                            'hora_limite'      => $actividad->hora_limite,
                            'hora_marcacion'   => $asistenciaActividad ? Carbon::parse($asistenciaActividad->hora_marcacion)->format('H:i:s') : 'No marcó',
                            'multa'            => $multa
                        ];
                    }

                    // Generar la llave dinámica para esta actividad en la fecha
                    $colKey = "d_{$fecha}_" . Str::slug($nombreActividad, '_');
                    $fila[$colKey] = [
                        'multa_total' => $multaActividad,
                        'detalle'     => $detalleActividad
                    ];
                    $totalMultasUsuario += $multaActividad;
                }
            }
            $fila['Total_Multas'] = $totalMultasUsuario;
            $reporte[] = $fila;
            Log::debug("Fila de reporte agregada", ['fila' => $fila]);
        }

        Log::info("Reporte generado para ministerio", [
            'ministerioId' => $ministerioId,
            'reporte'      => $reporte
        ]);

        return $reporte;
    }


    /**
     * Obtiene el arreglo de cabecera con las fechas, el día de la semana y las actividades.
     *
     * @param array $horariosPorFecha Resultado de obtenerHorariosPorMinisterio.
     * @return array
     */
    public function obtenerCabeceraFechas(array $horariosPorFecha)
    {
        $cabecera = [];
        foreach ($horariosPorFecha as $horario) {
            $fecha = $horario->fecha;
            // Si la fecha no se ha agregado aún, inicializamos el array
            if (!isset($cabecera[$fecha])) {
                $cabecera[$fecha] = [
                    'fecha'       => $fecha,
                    'dia_semana'  => $horario->dia_semana, // ya viene traducido
                    'actividades' => []
                ];
            }

            // Recorrer las actividades de ese día y agregar solo las únicas
            foreach ($horario->actividades as $actividad) {
                if (!in_array($actividad->nombre_actividad, $cabecera[$fecha]['actividades'])) {
                    $cabecera[$fecha]['actividades'][] = $actividad->nombre_actividad;
                }
            }
        }
        // ordenar las fechas cronológicamente
        ksort($cabecera);
        return $cabecera;
    }

    public function multaReporte(Request $request)
    {
        $dateRange = $request->input('date_range', now()->startOfMonth()->format('d-m-Y 00:00:00') . ' - ' . now()->endOfMonth()->format('d-m-Y 23:59:59'));

        // Separar fecha de inicio y fin
        [$startDate, $endDate] = explode(' - ', $dateRange);

        // Guardar el rango de fechas en la sesión
        $request->session()->put('date_range', $dateRange);

        // Convertir a formato correcto para la base de datos (YYYY-MM-DD)
        $startDate = Carbon::createFromFormat('d-m-Y H:i:s', trim($startDate))->format('Y-m-d H:i:s');
        $endDate = Carbon::createFromFormat('d-m-Y H:i:s', trim($endDate))->format('Y-m-d H:i:s');
        $deptId = $request->input('ministerio_id', 3);

        $pageTitle = 'Reporte de multas, desde el: '
            . Carbon::parse($startDate)->translatedFormat('d F Y')
            . ' hasta el: '
            . Carbon::parse($endDate)->translatedFormat('d F Y');

        // Generar el reporte dinámico
        $reporteDinamico = $this->generarReporteColumnasDinamicas($deptId, $startDate, $endDate);

        // Obtener los horarios por fecha
        $horariosPorFecha = $this->obtenerHorariosPorMinisterio($deptId, $startDate, $endDate);

        // Usar una función separada para generar la cabecera con las fechas y actividades
        $cabeceraFechas = $this->obtenerCabeceraFechas($horariosPorFecha);

        return view('admin.reportes.multa', compact('reporteDinamico', 'cabeceraFechas'));
    }
}
