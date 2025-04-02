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
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">

                        <!-- Reporte General dinamico -->
                        <div class="tab-pane fade show active" id="general">
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
                                                    <th rowspan="2">Total Productos</th>
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
                                                                        @php
                                                                            $detalle = $row[$colKey]['detalle'];
                                                                            $tienePermiso = collect($detalle)->contains(
                                                                                function ($d) {
                                                                                    return $d['permiso'] !== 'No';
                                                                                },
                                                                            );
                                                                        @endphp

                                                                        @if ($tienePermiso)
                                                                            <span class="badge badge-info">Permiso</span>
                                                                        @elseif ($row[$colKey]['productos'] > 0)
                                                                            <span
                                                                                class="badge badge-warning">Producto</span>
                                                                        @else
                                                                            {{ $row[$colKey]['multa_total'] }}
                                                                        @endif
                                                                    @else
                                                                        Sin datos
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                        @endforeach
                                                        <td>{{ $row['Total_Multas'] }}</td>
                                                        <td>{{ $row['Total_Productos'] }}</td>
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
                                        <table id="reporte-detallado-table"
                                            class="table table-striped table-bordered table-hover table-sm datatable text-center">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">Nombre</th>
                                                    <th rowspan="2">Apellido</th>
                                                    @foreach ($cabeceraFechas as $fecha => $datos)
                                                        <th colspan="{{ count($datos['actividades']) }}">
                                                            {{ $fecha }} ({{ $datos['dia_semana'] }})
                                                        </th>
                                                    @endforeach
                                                    <th rowspan="2">Total Multas</th>
                                                    <th rowspan="2">Total Productos</th>
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
                                                        @foreach ($cabeceraFechas as $fecha => $datos)
                                                            @foreach ($datos['actividades'] as $actividad)
                                                                @php
                                                                    $colKey =
                                                                        "d_{$fecha}_" . Str::slug($actividad, '_');
                                                                @endphp
                                                                <td>
                                                                    @if (isset($row[$colKey]))
                                                                        @foreach ($row[$colKey]['detalle'] as $item)
                                                                            <div class="border-bottom mb-1 pb-1">
                                                                                {{-- Mostrar permiso --}}
                                                                                @if (isset($item['permiso']) && is_array($item['permiso']))
                                                                                    <span
                                                                                        class="badge badge-info">Permiso</span><br>
                                                                                    <span
                                                                                        class="badge badge-dark">{{ $item['permiso']['tipo'] }}</span><br>
                                                                                    <small><strong>Motivo:</strong>
                                                                                        {{ $item['permiso']['motivo'] }}</small><br>
                                                                                @elseif (isset($item['permiso']) && $item['permiso'] !== 'No')
                                                                                    <span
                                                                                        class="badge badge-info">Permiso</span><br>
                                                                                @endif

                                                                                {{-- Mostrar datos si no tiene permiso --}}
                                                                                @if (!isset($item['permiso']) || $item['permiso'] === 'No')
                                                                                    <small><strong>H/Reg.:</strong>
                                                                                        {{ $item['hora_registro'] }}</small><br>
                                                                                    <small><strong>H/Mul.:</strong>
                                                                                        {{ $item['hora_multa'] }}</small><br>
                                                                                    <small><strong>H/Marc.:</strong>
                                                                                        {{ $item['hora_marcacion'] }}</small><br>
                                                                                    <small><strong>Min/R:</strong>
                                                                                        {{ $item['retraso_min'] ?? 0 }}</small><br>
                                                                                    <small><strong>Tipo Multa:</strong>
                                                                                        {{ $item['tipo_multa'] ?? '-' }}</small><br>

                                                                                    @if ($item['producto'])
                                                                                        <span
                                                                                            class="badge badge-warning">Producto</span><br>
                                                                                    @elseif($item['multa'] > 0)
                                                                                        <small><strong>Multa:</strong>
                                                                                            {{ $item['multa'] }}</small><br>
                                                                                    @else
                                                                                        <small><strong>Puntual</strong></small><br>
                                                                                    @endif
                                                                                @endif
                                                                            </div>
                                                                        @endforeach
                                                                    @else
                                                                        Sin datos
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                        @endforeach
                                                        <td>{{ $row['Total_Multas'] }}</td>
                                                        <td>{{ $row['Total_Productos'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>



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
