<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        // Obtener el rango de fechas enviado desde el formulario
        $dateRange = $request->input('date_range', now()->startOfMonth()->format('d-m-Y 00:00:00') . ' - ' . now()->endOfMonth()->format('d-m-Y 23:59:59'));

        // Separar fecha de inicio y fin
        [$startDate, $endDate] = explode(' - ', $dateRange);

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

        // Consulta detalles con parámetros dinámicos
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

                        -- Si es viernes y se atrasa, mostrar 'Debe producto'
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
                    END AS multa_bs,
                    CASE 
                        WHEN strftime('%w', marc.punch_time) = '5' AND TIME(marc.punch_time) > '19:30:00' THEN 1
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

        return view('admin.reportes.index', compact('ministerios', 'multas_detalle', 'multas_general', 'pageTitle', 'deptId', 'dateRange'));
    }
}
