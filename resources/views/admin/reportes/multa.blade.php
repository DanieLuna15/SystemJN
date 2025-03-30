@php
    use Illuminate\Support\Str;
@endphp
@extends('adminlte::page')

@section('title', $pageTitle)

{{-- Push extra CSS --}}

@section('content')
    <x-adminlte-card>
        @include('admin.reportes.form')
    </x-adminlte-card>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active" href="#general" data-toggle="tab" data-section="general">General</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#detallado" data-toggle="tab" data-section="detallado">Detalle</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#servicios" data-toggle="tab" data-section="servicios">Servicios</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#dinamico" data-toggle="tab" data-section="dinamico">Servicios</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Reporte General -->
                        <div class="active tab-pane" id="general">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="actividad_servicios-table"
                                            class="table table-striped table-bordered table-hover table-sm datatable text-center">
                                            <thead class="text-center">
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Apellido</th>
                                                    <th>Ministerio</th>
                                                    <th>Multa total (Bs)</th>
                                                    <th>Productos</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($multas_general as $empleado)
                                                    <tr>
                                                        <td>{{ $empleado->emp_firstname }}</td>
                                                        <td>{{ $empleado->emp_lastname }}</td>
                                                        <td>{{ $empleado->dept_name }}</td>
                                                        <td>{{ $empleado->total_multa_bs }}</td>
                                                        <td>{{ $empleado->productos_adeudados }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Reporte detallado -->
                        <div class="tab-pane" id="detallado">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="actividad_servicios-table"
                                            class="table table-striped table-bordered table-hover table-sm datatable text-center">
                                            <thead class="text-center">
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Apellido</th>
                                                    <th>Día de la semana</th>
                                                    <th>Fecha</th>
                                                    <th>Hora</th>
                                                    <th>Departamento</th>
                                                    <th>Multa (Bs) / Productos</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($multas_detalle as $empleado)
                                                    <tr>
                                                        <td>{{ $empleado->emp_firstname }}</td>
                                                        <td>{{ $empleado->emp_lastname }}</td>
                                                        <td>{{ $empleado->dia_semana }}</td>
                                                        <td>{{ $empleado->punch_date }}</td>
                                                        <td>{{ $empleado->punch_hour }}</td>
                                                        <td>{{ $empleado->dept_name }}</td>
                                                        <td>{{ $empleado->multa_bs }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Reporte tipo Pivot (Columnas dinámicas para cada día) -->
                        <div class="tab-pane" id="servicios">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="reporte-asistencias-table"
                                            class="table table-striped table-bordered table-hover table-sm datatable text-center">

                                            <thead>

                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Apellido</th>
                                                    <th>Departamento</th>
                                                    {{-- Se generan dinámicamente las cabeceras de cada fecha --}}
                                                    @foreach ($dates as $date)
                                                        <th>{{ "{$date['fecha']} - {$date['dia_semana_lit']}" }}</th>
                                                    @endforeach
                                                    <th>Total Multas</th>
                                                    <th>Observaciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($multas_detalle_reporte as $empleado)
                                                    <tr>
                                                        <td>{{ $empleado->emp_firstname }}</td>
                                                        <td>{{ $empleado->emp_lastname }}</td>
                                                        <td>{{ $empleado->dept_name }}</td>
                                                        {{-- Se muestran las multas correspondientes a cada fecha dinámica --}}
                                                        @foreach ($dates as $date)
                                                            <td>{{ $empleado->{$date['alias']} ?? 0 }}</td>
                                                        @endforeach
                                                        <td>{{ $empleado->Total_Multas ?? 0 }}</td>
                                                        <td></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reporte dinamico -->
                        {{-- <div class="tab-pane" id="dinamico">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="reporte-asistencias-table"
                                            class="table table-striped table-bordered table-hover table-sm datatable text-center">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">Nombre</th>
                                                    <th rowspan="2">Apellido</th>
                                                    <th rowspan="2">Ministerio</th>
                                                    @foreach ($cabeceraFechas as $fecha => $datos)
                                                        <th colspan="{{ count($datos['actividades']) }}">
                                                            {{ $fecha }} ({{ $datos['dia_semana'] }})
                                                        </th>
                                                    @endforeach
                                                    <th rowspan="2">Total Multas</th>
                                                </tr>
                                                <tr>
                                                    @foreach ($cabeceraFechas as $datos)
                                                        @foreach ($datos['actividades'] as $actividad)
                                                            <th>{{ $actividad }}</th>
                                                        @endforeach
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($reporteDinamico as $row)
                                                    <tr>
                                                        <td>{{ $row['nombre'] }}</td>
                                                        <td>{{ $row['apellido'] }}</td>
                                                        <td>{{ $row['ministerio'] }}</td>
                                                        @foreach ($cabeceraFechas as $fecha => $datos)
                                                            @foreach ($datos['actividades'] as $actividad)
                                                                @php
                                                                    $colKey =
                                                                        "d_{$fecha}_" . Str::slug($actividad, '_');
                                                                @endphp
                                                                <td>
                                                                    @if (isset($row[$colKey]))
                                                                        {{ $row[$colKey]['multa_total'] }}
                                                                    @else
                                                                        Sin datos
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                        @endforeach
                                                         @foreach ($cabeceraFechas as $fecha => $datos)
                                                            @foreach ($datos['actividades'] as $actividad)
                                                                @php
                                                                    $colKey =
                                                                        "d_{$fecha}_" . Str::slug($actividad, '_');
                                                                @endphp
                                                                <td>
                                                                    @if (isset($row[$colKey]))
                                                                        <p><strong>Total:</strong>
                                                                            {{ $row[$colKey]['multa_total'] }}</p>
                                                                        <ul class="list-unstyled">
                                                                            @foreach ($row[$colKey]['detalle'] as $detalle)
                                                                                <li>
                                                                                    <strong>{{ $detalle['nombre_actividad'] }}</strong><br>
                                                                                    Tipo: {{ $detalle['tipo'] }}<br>
                                                                                    Hora Registro:
                                                                                    {{ $detalle['hora_registro'] }}<br>
                                                                                    Hora Marcado:
                                                                                    {{ $detalle['hora_marcacion'] }}<br>
                                                                                    Multa: {{ $detalle['multa'] }}
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    @else
                                                                        Sin datos
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                        @endforeach 
                                                        <td>{{ $row['Total_Multas'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>


                                    </div>
                                </div>
                            </div>
                        </div> --}}

                        <!-- Reporte dinamico -->
                        <div class="tab-pane" id="dinamico">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="reporte-asistencias-table"
                                            class="table table-striped table-bordered table-hover table-sm datatable text-center">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">Nombre</th>
                                                    <th rowspan="2">Apellido</th>
                                                    <th rowspan="2">Ministerio</th>
                                                    @foreach ($cabeceraFechas as $fecha => $datos)
                                                        <th colspan="{{ count($datos['actividades']) }}">
                                                            {{ $fecha }} ({{ $datos['dia_semana'] }})
                                                        </th>
                                                    @endforeach
                                                    <th rowspan="2">Total Multas</th>
                                                </tr>
                                                <tr>
                                                    @foreach ($cabeceraFechas as $datos)
                                                        @foreach ($datos['actividades'] as $actividad)
                                                            <th>{{ $actividad }}</th>
                                                        @endforeach
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($reporteDinamico as $row)
                                                    <tr>
                                                        <td>{{ $row['nombre'] }}</td>
                                                        <td>{{ $row['apellido'] }}</td>
                                                        <td>{{ $row['ministerio'] }}</td>
                                                        @foreach ($cabeceraFechas as $fecha => $datos)
                                                            @foreach ($datos['actividades'] as $actividad)
                                                                @php
                                                                    $colKey =
                                                                        "d_{$fecha}_" . Str::slug($actividad, '_');
                                                                @endphp
                                                                <td>
                                                                    @if (isset($row[$colKey]))
                                                                        {{ $row[$colKey]['multa_total'] }}
                                                                    @else
                                                                        Sin datos
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                        @endforeach
                                                        <td>{{ $row['Total_Multas'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>



                        {{-- @php
                            // Prepara un arreglo con claves tipo "YYYY-MM-DD" y valores "YYYY-MM-DD Literal"
                            // Por ejemplo: "2025-03-02" => "2025-03-02 Domingo"
                            $fechasLetras = [];
                            foreach ($reporteDinamico as $reporte) {
                                foreach ($reporte as $key => $value) {
                                    if (strpos($key, 'd_') === 0 && is_array($value) && isset($value['dia_semana'])) {
                                        $fechaOnly = str_replace('d_', '', $key);
                                        $fechasLetras[$fechaOnly] = "{$fechaOnly} " . ucfirst($value['dia_semana']);
                                    }
                                }
                            }
                            ksort($fechasLetras);

                            // Calcular el máximo número de actividades por cada fecha, entre todos los registros
                            $maxActividades = [];
                            foreach ($fechasLetras as $fecha => $label) {
                                $max = 0;
                                foreach ($reporteDinamico as $row) {
                                    $colKey = 'd_' . $fecha;
                                    if (isset($row[$colKey]) && isset($row[$colKey]['detalle'])) {
                                        $count = count($row[$colKey]['detalle']);
                                        if ($count > $max) {
                                            $max = $count;
                                        }
                                    }
                                }
                                // Si no hay actividades, asignamos al menos 1 subcolumna para mostrar "Sin actividades"
                                $maxActividades[$fecha] = $max > 0 ? $max : 1;
                            }
                        @endphp

                        <!-- Reporte dinamico -->
                        <div class="tab-pane" id="dinamico">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="reporte-asistencias-table"
                                            class="table table-striped table-bordered table-hover table-sm datatable text-center">
                                            <thead>
                                                <!-- Primera fila de encabezado: datos fijos y encabezados por fecha con colspan -->
                                                <tr>
                                                    <th rowspan="2">Nombre</th>
                                                    <th rowspan="2">Apellido</th>
                                                    <th rowspan="2">Ministerio</th>
                                                    @foreach ($fechasLetras as $fecha => $label)
                                                        <th colspan="{{ $maxActividades[$fecha] }}">{{ $label }}
                                                        </th>
                                                    @endforeach
                                                    <th rowspan="2">Total Multas</th>
                                                </tr>
                                                <!-- Segunda fila de encabezado: subcolumnas para cada fecha -->
                                                <tr>
                                                    @foreach ($fechasLetras as $fecha => $label)
                                                        @for ($i = 0; $i < $maxActividades[$fecha]; $i++)
                                                            <th>Actividad {{ $i + 1 }}</th>
                                                        @endfor
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($reporteDinamico as $row)
                                                    <tr>
                                                        <td>{{ $row['nombre'] }}</td>
                                                        <td>{{ $row['apellido'] }}</td>
                                                        <td>{{ $row['ministerio'] }}</td>
                                                        @foreach ($fechasLetras as $fecha => $label)
                                                            @php
                                                                $colKey = 'd_' . $fecha;
                                                                $detalles = isset($row[$colKey]['detalle'])
                                                                    ? $row[$colKey]['detalle']
                                                                    : [];
                                                                $cantDetalle = count($detalles);
                                                                $max = $maxActividades[$fecha];
                                                            @endphp
                                                            @if ($cantDetalle > 0)
                                                                @foreach ($detalles as $detalle)
                                                                    <td class="text-center">
                                                                        <strong>{{ $detalle['nombre_actividad'] }}</strong><br>
                                                                        Tipo: {{ $detalle['tipo'] }}<br>
                                                                        Hora:
                                                                        {{ $detalle['hora_marcacion'] ?? 'No marcó' }}<br>
                                                                        Multa: {{ number_format($detalle['multa'], 2) }}
                                                                    </td>
                                                                @endforeach
                                                                
                                                                @for ($i = 0; $i < $max - $cantDetalle; $i++)
                                                                    <td></td>
                                                                @endfor
                                                            @else
                                                                
                                                                @for ($i = 0; $i < $max; $i++)
                                                                    <td>Sin actividades</td>
                                                                @endfor
                                                            @endif
                                                        @endforeach
                                                        <td>{{ number_format($row['Total_Multas'], 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div> --}}


                    </div>
                </div>
            </div>
        </div>
    </div>



@stop
@push('css')
@endpush

{{-- Push extra scripts --}}

@push('js')
@endpush

@push('breadcrumb-plugins')
    {{-- @can('crear ministerios') --}}
    <a href="{{ route('admin.reportes.exportar') }}" class="btn btn-info rounded">
        <i class="fas fa-file-export"></i> Exportar Reporte
    </a>
    {{-- @endcan --}}
@endpush
