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

        // Consulta para obtener los detalles de las multas
        $multas_detalle = DB::connection('sqlite')->select("
        WITH primeras_marcaciones_viernes AS (
            SELECT emp_id, MIN(punch_time) AS punch_time
            FROM att_punches
            WHERE strftime('%w', punch_time) = '5' AND TIME(punch_time) >= '19:30:00'
            GROUP BY emp_id, DATE(punch_time)
        )
        SELECT 
            m.emp_firstname, 
            m.emp_lastname,         
            DATE(marc.punch_time) AS punch_date, 
            TIME(marc.punch_time) AS punch_hour,
            d.dept_name, 
            CASE strftime('%w', marc.punch_time)
                WHEN '0' THEN 'Domingo'
                WHEN '1' THEN 'Lunes'
                WHEN '2' THEN 'Martes'
                WHEN '3' THEN 'Miércoles'
                WHEN '4' THEN 'Jueves'
                WHEN '5' THEN 'Viernes'
                WHEN '6' THEN 'Sábado'
            END AS dia_semana,

            CASE 
                -- Excepción para el 21 de febrero de 2025: solo contar después de las 22:15
                WHEN DATE(marc.punch_time) = '2025-02-21' AND TIME(marc.punch_time) <= '22:15:00' THEN '0'
                
                 -- Aplicar regla a todos los viernes: solo contar la primera marcación después de las 19:30
                WHEN strftime('%w', marc.punch_time) = '5' 
                     AND NOT EXISTS (
                         SELECT 1 
                         FROM primeras_marcaciones_viernes p 
                         WHERE p.emp_id = marc.emp_id 
                         AND p.punch_time = marc.punch_time
                     ) 
                THEN '0'

                -- Si es viernes y se atrasa, mostrar 'Debe producto' en lugar de calcular multa
                WHEN strftime('%w', marc.punch_time) = '5' AND TIME(marc.punch_time) > '19:30:00' 
                THEN 'Debe producto'

                -- Multa para los jueves después de las 19:15
                WHEN strftime('%w', marc.punch_time) = '4' AND TIME(marc.punch_time) > '19:15:00' 
                THEN 
                    CASE 
                        WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 19:15:00')) > 1800 
                        THEN '20' 
                        ELSE (CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 19:15:00')) / 300 AS INTEGER) + 1) * 2
                    END

                -- Multas para el domingo (día 0)
                WHEN strftime('%w', marc.punch_time) = '0' THEN 
                    CASE 
                        -- 1ra marcación (07:45 a 10:00)
                        WHEN TIME(marc.punch_time) > '07:45:00' AND TIME(marc.punch_time) <= '10:00:00' 
                        THEN 
                            CASE 
                                WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 07:45:00')) > 1800 
                                THEN '20'
                                ELSE (CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 07:45:00')) / 300 AS INTEGER) + 1) * 2
                            END

                        -- 2da marcación (10:45 a 13:00)
                        WHEN TIME(marc.punch_time) > '10:45:00' AND TIME(marc.punch_time) <= '13:00:00' 
                        THEN 
                            CASE 
                                WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 10:45:00')) > 1800 
                                THEN '20'
                                ELSE (CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 10:45:00')) / 300 AS INTEGER) + 1) * 2
                            END

                        -- 3ra marcación (14:45 a 17:00)
                        WHEN TIME(marc.punch_time) > '14:45:00' AND TIME(marc.punch_time) <= '18:00:00' 
                        THEN 
                            CASE 
                                WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 14:45:00')) > 1800 
                                THEN '20'
                                ELSE (CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 14:45:00')) / 300 AS INTEGER) + 1) * 2
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

        // Exportar los resultados a Excel
        return Excel::download(new MultasExport($multas_detalle), 'reporte_multas.xlsx');
    }

    public function multa(Request $request)
    {
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
        // $multas_detalle = DB::connection('sqlite')->select("
        //     WITH primeras_marcaciones_viernes AS (
        //         SELECT emp_id, MIN(punch_time) AS punch_time
        //         FROM att_punches
        //         WHERE strftime('%w', punch_time) = '5' AND TIME(punch_time) >= '19:30:00'
        //         GROUP BY emp_id, DATE(punch_time)
        //     )
        //     SELECT 
        //         m.emp_firstname, 
        //         m.emp_lastname,         
        //         DATE(marc.punch_time) AS punch_date, 
        //         TIME(marc.punch_time) AS punch_hour,
        //         d.dept_name, 
        //         CASE strftime('%w', marc.punch_time)
        //             WHEN '0' THEN 'Domingo'
        //             WHEN '1' THEN 'Lunes'
        //             WHEN '2' THEN 'Martes'
        //             WHEN '3' THEN 'Miércoles'
        //             WHEN '4' THEN 'Jueves'
        //             WHEN '5' THEN 'Viernes'
        //             WHEN '6' THEN 'Sábado'
        //         END AS dia_semana,
        //         CASE 
        //             " . implode(' ', $act_eventuales->map(function ($eventual) {
        //                 return "WHEN DATE(marc.punch_time) = '{$eventual->fecha}' AND TIME(marc.punch_time) <= '{$eventual->hora_multa}' THEN '0'";
        //             })->toArray()) . "
        
        //             WHEN strftime('%w', marc.punch_time) = '5' 
        //                 AND NOT EXISTS (
        //                     SELECT 1 
        //                     FROM primeras_marcaciones_viernes p 
        //                     WHERE p.emp_id = marc.emp_id 
        //                     AND p.punch_time = marc.punch_time
        //                 ) 
        //             THEN '0'
        
        //             WHEN strftime('%w', marc.punch_time) = '5' AND TIME(marc.punch_time) > '19:30:00' 
        //             THEN 'Debe producto'
        
        //             " . implode(' ', $horario_fijo_jueves->map(function ($fijo) {
        //                 return " WHEN strftime('%w', marc.punch_time) = '{$fijo->dia_semana}' AND TIME(marc.punch_time) > '{$fijo->hora_multa}' 
        //                         THEN 
        //                             CASE 
        //                                 WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || '{$fijo->hora_multa}')) > 1800 
        //                                 THEN '20' 
        //                                 ELSE (CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || '{$fijo->hora_multa}')) / 300 AS INTEGER) + 1) * 2
        //                             END ";
        //             })->toArray()) . "
        
        //             WHEN strftime('%w', marc.punch_time) = '0' THEN 
        //                 CASE 
        //                     WHEN TIME(marc.punch_time) > '07:45:59' AND TIME(marc.punch_time) <= '10:00:00' 
        //                     THEN 
        //                         CASE 
        //                             WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 07:45:59')) > 1800 
        //                             THEN '20'
        //                             ELSE (CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 07:45:59')) / 300 AS INTEGER) + 1) * 2
        //                         END
                        
        //                     WHEN TIME(marc.punch_time) > '10:45:59' AND TIME(marc.punch_time) <= '13:00:00' 
        //                     THEN 
        //                         CASE 
        //                             WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 10:45:59')) > 1800 
        //                             THEN '20'
        //                             ELSE (CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 10:45:59')) / 300 AS INTEGER) + 1) * 2
        //                         END
        
        //                     WHEN TIME(marc.punch_time) > '14:45:59' AND TIME(marc.punch_time) <= '18:00:00' 
        //                     THEN 
        //                         CASE 
        //                             WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 14:45:59')) > 1800 
        //                             THEN '20'
        //                             ELSE (CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 14:45:59')) / 300 AS INTEGER) + 1) * 2
        //                         END
        //                     ELSE '0'
        //                 END
        //             ELSE '0'
        //         END AS multa_bs
        //     FROM hr_employee AS m
        //     LEFT JOIN att_punches AS marc 
        //         ON m.id = marc.emp_id 
        //         AND marc.punch_time BETWEEN ? AND ? 
        //     INNER JOIN hr_department AS d ON m.emp_dept = d.id
        
        //     -- REGLA: Si un empleado no marcó en ninguna hora esperada, debe 40 Bs
        //     UNION
        //     SELECT 
        //         m.emp_firstname, 
        //         m.emp_lastname,         
        //         NULL AS punch_date, 
        //         NULL AS punch_hour,
        //         d.dept_name, 
        //         'Sin marcación' AS dia_semana,
        //         '40' AS multa_bs
        //     FROM hr_employee AS m
        //     INNER JOIN hr_department AS d ON m.emp_dept = d.id
        //     WHERE NOT EXISTS (
        //         SELECT 1 
        //         FROM att_punches AS marc 
        //         WHERE marc.emp_id = m.id 
        //         AND marc.punch_time BETWEEN ? AND ?
        //     ) 
        //     AND d.id = ?
            
        //     ORDER BY punch_date ASC NULLS LAST;
        // ", [
        //     $startDate, $endDate,
        //     $startDate, $endDate, $deptId
        // ]);
    
        $multas_detalle = DB::connection('sqlite')->select("
            WITH primeras_marcaciones_viernes AS (
                SELECT emp_id, MIN(punch_time) AS punch_time
                FROM att_punches
                WHERE strftime('%w', punch_time) = '5' AND TIME(punch_time) >= '19:30:00'
                GROUP BY emp_id, DATE(punch_time)
            )
            SELECT 
                m.emp_firstname, 
                m.emp_lastname,         
                DATE(marc.punch_time) AS punch_date, 
                TIME(marc.punch_time) AS punch_hour,
                d.dept_name, 
                CASE strftime('%w', marc.punch_time)
                    WHEN '0' THEN 'Domingo'
                    WHEN '1' THEN 'Lunes'
                    WHEN '2' THEN 'Martes'
                    WHEN '3' THEN 'Miércoles'
                    WHEN '4' THEN 'Jueves'
                    WHEN '5' THEN 'Viernes'
                    WHEN '6' THEN 'Sábado'
                END AS dia_semana,
                CASE 
                    -- Usamos un CASE para las fechas dinámicas
                    " . implode(' ', $act_eventuales->map(function ($eventual) {
                        return "WHEN DATE(marc.punch_time) = '{$eventual->fecha}' AND TIME(marc.punch_time) <= '{$eventual->hora_multa}' THEN '0'";
                    })->toArray()) . "
            
                    -- Aplicar regla a todos los viernes: solo contar la primera marcación después de las 19:30:59
                    WHEN strftime('%w', marc.punch_time) = '5' 
                        AND NOT EXISTS (
                            SELECT 1 
                            FROM primeras_marcaciones_viernes p 
                            WHERE p.emp_id = marc.emp_id 
                            AND p.punch_time = marc.punch_time
                        ) 
                    THEN '0'
            
                    -- Si es viernes y se atrasa, mostrar 'Debe producto' en lugar de calcular multa
                    WHEN strftime('%w', marc.punch_time) = '5' AND TIME(marc.punch_time) > '19:30:00' 
                    THEN 'Debe producto'

                    -- Multa para los jueves después de las 19:15:59
                        " . implode(' ', $horario_fijo_jueves->map(function ($fijo) {
                        return " WHEN strftime('%w', marc.punch_time) = '{$fijo->dia_semana}' AND TIME(marc.punch_time) > '{$fijo->hora_multa}' 
                                    THEN 
                                        CASE 
                                            WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || '{$fijo->hora_multa}')) > 1800 
                                            THEN '20' 
                                            ELSE (CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || '{$fijo->hora_multa}')) / 300 AS INTEGER) + 1) * 2
                                        END ";
                    })->toArray()) . "
            
                    -- Multas para el domingo (día 0)
                    WHEN strftime('%w', marc.punch_time) = '0' THEN 
                        CASE 
                            -- 1ra marcación (07:45:59 a 10:00)
                            WHEN TIME(marc.punch_time) > '07:45:59' AND TIME(marc.punch_time) <= '10:00:00' 
                            THEN 
                                CASE 
                                    WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 07:45:59')) > 1800 
                                    THEN '20'
                                    ELSE (CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 07:45:59')) / 300 AS INTEGER) + 1) * 2
                                END
                        
                            -- 2da marcación (10:45:59 a 13:00)
                            WHEN TIME(marc.punch_time) > '10:45:59' AND TIME(marc.punch_time) <= '13:00:00' 
                            THEN 
                                CASE 
                                    WHEN (strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 10:45:59')) > 1800 
                                    THEN '20'
                                    ELSE (CAST((strftime('%s', marc.punch_time) - strftime('%s', DATE(marc.punch_time) || ' 10:45:59')) / 300 AS INTEGER) + 1) * 2
                                END
            
                            -- 3ra marcación (14:45:59 a 17:00)
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
        ", [
            $startDate,
            $endDate,
            $deptId
        ]);

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
                        -- Excepción para el 21 de febrero de 2025: solo contar después de las 22:15:59
                        WHEN DATE(marc.punch_time) = '2025-02-21' AND TIME(marc.punch_time) <= '22:15:59' THEN '0'

                        -- Aplicar regla a todos los viernes: solo contar la primera marcación después de las 19:30
                        WHEN strftime('%w', marc.punch_time) = '5' 
                             AND NOT EXISTS (
                                 SELECT 1 
                                 FROM primeras_marcaciones_viernes p 
                                 WHERE p.emp_id = marc.emp_id 
                                 AND p.punch_time = marc.punch_time
                             ) 
                        THEN '0'

                        -- Si es viernes y se atrasa, mostrar 'Debe producto'
                        WHEN strftime('%w', marc.punch_time) = '5' AND TIME(marc.punch_time) > '19:30:59' 
                        THEN 'Debe producto'

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


        return view('admin.reportes.multa', compact('ministerios', 'multas_detalle', 'multas_general', 'pageTitle', 'deptId', 'dateRange'));
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
