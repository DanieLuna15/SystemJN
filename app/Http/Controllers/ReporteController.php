<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Horario;
use App\Models\Permiso;
use Carbon\CarbonPeriod;
use App\Constants\Status;
use App\Models\Excepcion;
use App\Models\Asistencia;
use App\Models\Ministerio;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\ReporteExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

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
        //dd($multas_detalle);

        // Obtener los horarios por fecha
        $horariosPorFecha = $this->obtenerHorariosPorMinisterio($ministerioId, $startDate, $endDate);
        //dd($horariosPorFecha);
        // Usar una función separada para generar la cabecera con las fechas y actividades
        $dates = $this->obtenerCabeceraFechas($horariosPorFecha);
        //dd($dates);

        $excepciones = Excepcion::whereHas('ministerios', function ($query) use ($ministerioId) {
            $query->where('ministerio_id', $ministerioId);  // Filtra por el ministerio específico
        })
            ->where('estado', Status::ACTIVE)  // Filtra por estado activo
            ->whereBetween('fecha', [$startDate, $endDate])  // Filtra por rango de fechas
            ->orderByDesc('id')
            ->get();
        //dd($excepciones->toArray());

        $pageTitle = 'Reporte de multas ('
            . Carbon::parse($startDate)->translatedFormat('d M')
            . ' - '
            . Carbon::parse($endDate)->translatedFormat('d M')
            . ') '
            . Carbon::parse($endDate)->format('Y'); // ✅ Agrega el año al título

        $fileName = 'reporte_multas_' . Carbon::parse($startDate)->format('Y-m-d') . '_a_' . Carbon::parse($endDate)->format('Y-m-d') . '.xlsx';

        // Exportar los resultados a Excel con el nombre dinámico del archivo
        return Excel::download(new ReporteExport($multas_detalle, $dates, $excepciones, $pageTitle), $fileName);
    }

    public function multa(Request $request)
    {
        try {
            $dateRange = $request->input('date_range', now()->startOfMonth()->format('d-m-Y 00:00:00') . ' - ' . now()->format('d-m-Y 23:59:59'));
            [$startDate, $endDate] = explode(' - ', $dateRange);

            $request->session()->put('date_range', $dateRange);

            $startDate = Carbon::createFromFormat('d-m-Y H:i:s', trim($startDate))->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y H:i:s', trim($endDate))->format('Y-m-d H:i:s');
            $ministerioId = $request->input('ministerio_id', 1);

            $pageTitle = 'Reporte de multas, desde el: '
                . Carbon::parse($startDate)->translatedFormat('d F Y')
                . ' hasta el: '
                . Carbon::parse($endDate)->translatedFormat('d F Y');

            $ministerios = Ministerio::where('estado', Status::ACTIVE)->get();

            // Esta es la parte que puede lanzar una excepción
            $reporteDinamico = $this->generarReporteColumnasDinamicas($ministerioId, $startDate, $endDate);
            //dd($reporteDinamico);
            $horariosPorFecha = $this->obtenerHorariosPorMinisterio($ministerioId, $startDate, $endDate);
            $cabeceraFechas = $this->obtenerCabeceraFechas($horariosPorFecha);

            return view('admin.reportes.multa', compact(
                'ministerios',
                'pageTitle',
                'ministerioId',
                'dateRange',
                'reporteDinamico',
                'cabeceraFechas'
            ));
        } catch (\Exception $e) {
            // Retornamos la vista con el error como variable de sesión
            return back()->withInput()->with('errorRegla', $e->getMessage());
        }
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
        $horarios = $this->getHorariosActivos($ministerioId);
        $excepciones = $this->getExcepcionesPorMinisterio($ministerioId, $startDate, $endDate);

        $resultados = $this->procesarHorariosFijos($horarios, $excepciones, $startDate, $endDate);
        $resultados = $this->procesarHorariosEventuales($horarios, $startDate, $endDate, $resultados);

        Log::info("Horarios obtenidos para ministerio {$ministerioId}", [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'resultados' => $resultados
        ]);

        return array_values($resultados);
    }

    private function getHorariosActivos($ministerioId)
    {
        return Ministerio::findOrFail($ministerioId)
            ->horarios()
            ->where('estado', Status::ACTIVE)
            ->with('actividadServicio:id,nombre')
            ->get();
    }

    private function getExcepcionesPorMinisterio($ministerioId, $startDate, $endDate)
    {
        return \App\Models\Excepcion::where('estado', true)
            ->whereHas('ministerios', function ($q) use ($ministerioId) {
                $q->where('ministerio_id', $ministerioId);
            })
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('fecha', [Carbon::parse($startDate)->format('Y-m-d'), Carbon::parse($endDate)->format('Y-m-d')])
                    ->orWhere(function ($q) use ($startDate) {
                        $q->where('fecha', '<=', Carbon::parse($startDate)->format('Y-m-d'))
                            ->where('hasta', '>=', Carbon::parse($startDate)->format('Y-m-d'));
                    });
            })
            ->get();
    }

    private function procesarHorariosFijos($horarios, $excepciones, $startDate, $endDate)
    {
        $dayMapping = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $resultados = [];
        $periodo = CarbonPeriod::create($startDate, $endDate);

        foreach ($periodo as $fecha) {
            $fechaString = $fecha->format('Y-m-d');
            $diaIndice = $fecha->dayOfWeek; // 0=Domingo, 6=Sábado

            // Excepciones del día
            $excepcionesHoy = $excepciones->filter(function ($excepcion) use ($fechaString) {
                $inicio = Carbon::parse($excepcion->fecha)->format('Y-m-d');
                $fin = $excepcion->hasta ? Carbon::parse($excepcion->hasta)->format('Y-m-d') : $inicio;
                return $fechaString >= $inicio && $fechaString <= $fin;
            });

            $excepcionDiaCompleto = $excepcionesHoy->first(fn($ex) => in_array($ex->dia_entero, [1, 2]));

            if (!$excepcionDiaCompleto) {
                $horariosFijos = $horarios->where('tipo', 1)->where('dia_semana', $diaIndice);

                if ($horariosFijos->isNotEmpty()) {
                    $actividades = $horariosFijos->map(function ($h) use ($excepcionesHoy) {
                        $excepcionParcial = $excepcionesHoy->first(fn($ex) => $ex->dia_entero == 0 && $ex->hora_inicio && $ex->hora_fin);

                        if ($excepcionParcial) {
                            $horaRegistro = Carbon::parse($h->hora_registro);
                            $horaInicioEx = Carbon::parse($excepcionParcial->hora_inicio);
                            $horaFinEx = Carbon::parse($excepcionParcial->hora_fin);

                            if ($horaRegistro->between($horaInicioEx, $horaFinEx, true)) {
                                return null;
                            }
                        }
                        return $h;
                    })->filter();

                    if ($actividades->isNotEmpty()) {
                        $resultados[$fechaString] = (object) [
                            'fecha' => $fechaString,
                            'dia_semana' => $dayMapping[$diaIndice],
                            'actividades' => $actividades->map(fn($h) => (object) [
                                'tipo' => 'fijo',
                                'nombre_actividad' => $h->actividadServicio->nombre ?? null,
                                'hora_registro' => $h->hora_registro,
                                'hora_multa' => $h->hora_multa,
                                'hora_limite' => $h->hora_limite,
                                'tipo_pago' => $h->tipo_pago
                            ])->values()->toArray()
                        ];
                    }
                }
            }
        }

        return $resultados;
    }

    private function procesarHorariosEventuales($horarios, $startDate, $endDate, $resultados)
    {
        $dayMapping = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

        $horariosEventuales = $horarios->where('tipo', 0)
            ->whereNotNull('fecha')
            ->filter(function ($h) use ($startDate, $endDate) {
                return Carbon::parse($h->fecha)->between(
                    Carbon::parse($startDate)->format('Y-m-d'),
                    Carbon::parse($endDate)->format('Y-m-d'),
                    true
                );
            });

        foreach ($horariosEventuales as $horario) {
            $fechaString = Carbon::parse($horario->fecha)->format('Y-m-d');
            $diaIndice = Carbon::parse($horario->fecha)->dayOfWeek;

            if (!isset($resultados[$fechaString])) {
                $resultados[$fechaString] = (object) [
                    'fecha' => $fechaString,
                    'dia_semana' => $dayMapping[$diaIndice],
                    'actividades' => []
                ];
            }

            $resultados[$fechaString]->actividades[] = (object) [
                'tipo' => 'eventual',
                'nombre_actividad' => $horario->actividadServicio->nombre ?? null,
                'hora_registro' => $horario->hora_registro,
                'hora_multa' => $horario->hora_multa,
                'hora_limite' => $horario->hora_limite,
                'tipo_pago' => $horario->tipo_pago
            ];
        }

        return $resultados;
    }


    /**
     * Calcula la multa para una asistencia, basándose en el horario (o regla por defecto) y la regla de multa.
     * Si la asistencia es nula, se aplica la multa por falta.
     */
    protected function calcularMulta($asistencia, $horario, $reglaMulta)
    {
        Log::debug("Iniciando cálculo de multa", [
            'asistencia' => $asistencia,
            'horario' => $horario,
            'reglaMulta' => $reglaMulta
        ]);

        $esProducto = $horario->tipo_pago == 0;
        $resultado = [
            'tipo' => null,
            'multa' => 0,
            'producto' => false, // True si debe un producto
            'hora_marcacion' => null,
            'retraso_min' => null
        ];

        if (!$asistencia) {
            Log::info("No se encontró asistencia. Aplicando multa por falta o producto.", [
                'tipo_pago' => $horario->tipo_pago
            ]);

            $resultado['tipo'] = 'falta';
            $resultado['multa'] = $esProducto ? 0 : $reglaMulta->multa_por_falta;
            $resultado['producto'] = $esProducto;
            return $resultado;
        }

        $horaRegistro = Carbon::parse($horario->hora_registro);
        $horaMulta = Carbon::parse($horario->hora_multa);
        $horaLimite = Carbon::parse($horario->hora_limite);
        $horaMarcacion = Carbon::parse($asistencia->hora_marcacion);

        $resultado['hora_marcacion'] = $horaMarcacion->format('H:i:s');

        // Puntualidad
        if ($horaMarcacion->lessThanOrEqualTo($horaRegistro)) {
            $resultado['tipo'] = 'puntual';
            return $resultado;
        }

        if ($horaMarcacion->lessThanOrEqualTo($horaMulta)) {
            $resultado['tipo'] = 'puntual';
            return $resultado;
        }

        // Retraso
        $retraso = $horaMarcacion->greaterThan($horaLimite)
            ? $horaLimite->diffInMinutes($horaMulta)
            : $horaMarcacion->diffInMinutes($horaMulta);

        $resultado['retraso_min'] = $retraso;

        if ($retraso >= $reglaMulta->minutos_retraso_largo) {
            $resultado['tipo'] = 'retraso_largo';
            $resultado['multa'] = $esProducto ? 0 : $reglaMulta->multa_por_retraso_largo;
            $resultado['producto'] = $esProducto;
            return $resultado;
        }

        // Retraso corto
        $intervalos = ceil($retraso / $reglaMulta->minutos_por_incremento);
        $multaIncremental = $intervalos * $reglaMulta->multa_incremental;

        $resultado['tipo'] = 'retraso';
        $resultado['multa'] = $esProducto ? 0 : $multaIncremental;
        $resultado['producto'] = $esProducto;

        return $resultado;
    }

    /**
     * Obtiene los permisos autorizados para los usuarios en un rango de fechas.
     */
    private function getPermisosPorUsuarios($usuarioIds, $startDate, $endDate)
    {
        return Permiso::where('estado', 1)
            ->whereHas('usuarios', function ($q) use ($usuarioIds) {
                $q->whereIn('usuario_id', $usuarioIds);
            })
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('fecha', [Carbon::parse($startDate)->format('Y-m-d'), Carbon::parse($endDate)->format('Y-m-d')])
                    ->orWhere(function ($q) use ($startDate) {
                        $q->where('fecha', '<=', Carbon::parse($startDate)->format('Y-m-d'))
                            ->where('hasta', '>=', Carbon::parse($startDate)->format('Y-m-d'));
                    });
            })
            ->with('usuarios')
            ->get();
    }

    /**
     * Genera el reporte de multas con columnas dinámicas.
     * Cada fila representa un usuario y cada columna una fecha (con alias d_YYYY-MM-DD).
     * En cada fecha se muestra un arreglo con el total del día y el detalle por cada horario.
     */

    public function generarReporteColumnasDinamicas($ministerioId, $startDate, $endDate)
    {
        $ministerio = Ministerio::findOrFail($ministerioId);
        $reglaMulta = $ministerio->reglasMultas()->where('estado', 1)->first();

        if (!$reglaMulta) {
            throw new \Exception("No se encontró una regla de multa para el ministerio: " . $ministerio->nombre);
        }

        //$usuarios = $ministerio->usuarios;
        $usuarios = $ministerio->usuarios()
            ->where('estado', Status::ACTIVE)
            ->orderBy('last_name', 'asc') // Ordena por apellido ascendente
            ->get();

        $usuarioIds = $usuarios->pluck('id')->toArray();

        $permisos = $this->getPermisosPorUsuarios($usuarioIds, $startDate, $endDate);
        $horariosPorFecha = $this->obtenerHorariosPorMinisterio($ministerioId, $startDate, $endDate);
        $fechas = array_unique(array_map(fn($item) => $item->fecha, $horariosPorFecha));
        sort($fechas);

        $ciUsuarios = $usuarios->pluck('ci')->toArray();
        $asistenciasQuery = Asistencia::whereIn('ci', $ciUsuarios)
            ->whereBetween('fecha', [$startDate, $endDate])
            ->get();

        $asistencias = $asistenciasQuery->groupBy(function ($item) {
            return $item->ci . '_' . Carbon::parse($item->fecha)->format('Y-m-d');
        });

        $reporte = [];

        foreach ($usuarios as $usuario) {
            $permisosUsuario = $permisos->filter(function ($permiso) use ($usuario) {
                return $permiso->usuarios->contains('id', $usuario->id);
            })->values(); 

            $fila = [
                'integrantes' => $usuario->last_name . ' ' . $usuario->name,
                'ministerio' => $usuario->ministerios->firstWhere('id', $ministerioId)->nombre ?? 'No asignado',
                'Total_Multas' => 0,
                'Total_Productos' => 0,
                'permisos' => $permisosUsuario->map(function ($permiso) {
                    return [
                        'id' => $permiso->id,
                        'motivo' => $permiso->motivo,
                        'fecha' => $permiso->fecha,
                        'hasta' => $permiso->hasta,
                        'dia_entero' => $permiso->dia_entero,
                        'hora_inicio' => $permiso->hora_inicio,
                        'hora_fin' => $permiso->hora_fin,
                    ];
                })->toArray()
            ];

            $totalMultasUsuario = 0;
            $totalProductosUsuario = 0;

            foreach ($fechas as $fecha) {
                $key = $usuario->ci . '_' . $fecha;
                $asistenciasDia = $asistencias[$key] ?? collect();

                $permisosHoy = $permisos->filter(function ($permiso) use ($usuario, $fecha) {
                    $fechaString = Carbon::parse($fecha)->format('Y-m-d');
                    $fechaInicio = Carbon::parse($permiso->fecha)->format('Y-m-d');
                    $fechaFin = $permiso->hasta ? Carbon::parse($permiso->hasta)->format('Y-m-d') : $fechaInicio;
                    return $permiso->usuarios->contains('id', $usuario->id)
                        && $fechaString >= $fechaInicio
                        && $fechaString <= $fechaFin;
                });

                $horariosDia = array_filter($horariosPorFecha, fn($h) => $h->fecha === $fecha);

                $actividadesGroup = [];
                foreach ($horariosDia as $horario) {
                    foreach ($horario->actividades as $actividad) {
                        $nombreActividad = $actividad->nombre_actividad ?? 'Sin nombre';
                        if (!isset($actividadesGroup[$nombreActividad])) {
                            $actividadesGroup[$nombreActividad] = [];
                        }
                        $actividadesGroup[$nombreActividad][] = $actividad;
                    }
                }

                foreach ($actividadesGroup as $nombreActividad => $actividadesArray) {
                    $multaActividad = 0;
                    $productosActividad = 0;
                    $detalleActividad = [];

                    foreach ($actividadesArray as $actividad) {
                        $horaRegistro = Carbon::parse($actividad->hora_registro);

                        $permisoAplicado = 'No';
                        $permisoInfo = null;

                        $permisoDiaCompleto = $permisosHoy->first(fn($p) => $p->dia_entero == 1);
                        $permisoRangoDias = $permisosHoy->first(fn($p) => $p->dia_entero == 2);
                        $permisoRangoHoras = $permisosHoy->first(fn($p) => $p->dia_entero == 0 && $p->hora_inicio && $p->hora_fin);

                        if ($permisoDiaCompleto) {
                            $permisoAplicado = 'Sí';
                            $permisoInfo = [
                                'tipo' => 'Día entero',
                                'motivo' => $permisoDiaCompleto->motivo
                            ];
                        } elseif ($permisoRangoDias) {
                            $permisoAplicado = 'Sí';
                            $permisoInfo = [
                                'tipo' => 'Rango de días',
                                'motivo' => $permisoRangoDias->motivo
                            ];
                        } elseif ($permisoRangoHoras) {
                            $horaInicio = Carbon::parse($permisoRangoHoras->hora_inicio);
                            $horaFin = Carbon::parse($permisoRangoHoras->hora_fin);
                            if ($horaRegistro->between($horaInicio, $horaFin, true)) {
                                $permisoAplicado = 'Sí';
                                $permisoInfo = [
                                    'tipo' => 'Rango de horas',
                                    'motivo' => $permisoRangoHoras->motivo
                                ];
                            }
                        }

                        if ($permisoInfo) {
                            $detalleActividad[] = [
                                'nombre_actividad' => $nombreActividad,
                                'permiso' => $permisoInfo,
                                'multa' => 0,
                                'producto' => false
                            ];
                            continue;
                        }

                        $asistenciasEnRango = $asistenciasDia->filter(function ($a) use ($horaRegistro) {
                            $horaMarcacion = Carbon::parse($a->hora_marcacion);
                            return $horaMarcacion->greaterThanOrEqualTo($horaRegistro);
                        });

                        $asistenciaActividad = $asistenciasEnRango->sortBy(fn($a) => Carbon::parse($a->hora_marcacion)->timestamp)->first();

                        $resultado = $this->calcularMulta($asistenciaActividad, $actividad, $reglaMulta);

                        $multaActividad += $resultado['multa'];
                        if ($resultado['producto']) {
                            $productosActividad++;
                        }

                        $detalleActividad[] = [
                            'nombre_actividad' => $nombreActividad,
                            'tipo' => $actividad->tipo,
                            'tipo_pago' => $actividad->tipo_pago ?? 1,
                            'hora_registro' => $actividad->hora_registro,
                            'hora_multa' => $actividad->hora_multa,
                            'hora_limite' => $actividad->hora_limite,
                            'hora_marcacion' => $resultado['hora_marcacion'] ?? 'No marcó',
                            'retraso_min' => $resultado['retraso_min'],
                            'tipo_multa' => $resultado['tipo'],
                            'multa' => $resultado['multa'],
                            'producto' => $resultado['producto'],
                            'permiso' => 'No'
                        ];
                    }

                    $colKey = "d_{$fecha}_" . Str::slug($nombreActividad, '_');
                    $fila[$colKey] = [
                        'multa_total' => $multaActividad,
                        'productos' => $productosActividad,
                        'detalle' => $detalleActividad
                    ];

                    $totalMultasUsuario += $multaActividad;
                    $totalProductosUsuario += $productosActividad;
                }
            }

            $fila['Total_Multas'] = $totalMultasUsuario;
            $fila['Total_Productos'] = $totalProductosUsuario;
            $reporte[] = $fila;
        }

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
                    'fecha' => $fecha,
                    'dia_semana' => $horario->dia_semana, // ya viene traducido
                    'actividades' => []
                ];
            }

            // Recorrer las actividades de ese día y agregar solo las únicas
            foreach ($horario->actividades as $actividad) {
                if (!in_array($actividad->nombre_actividad, $cabecera[$fecha]['actividades'])) {
                    $cabecera[$fecha]['actividades'][] = [
                        'nombre_actividad' => $actividad->nombre_actividad,
                        'tipo' => $actividad->tipo
                    ];
                }
            }
        }
        // ordenar las fechas cronológicamente
        ksort($cabecera);
        //dd($cabecera);
        return $cabecera;
    }
}
