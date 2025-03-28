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
                            <a class="nav-link" href="#detallado" data-toggle="tab" data-section="detallado">Detallado</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#servicios" data-toggle="tab" data-section="servicios">Servicios</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#servicios" data-toggle="tab" data-section="servicios">Reunion de Lideres</a>
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
                                                    {{-- Cabecera dinámica de fechas --}}
                                                    @foreach ($dates as $date)
                                                        @if ($date['dia_semana'] == '0')
                                                            <!-- Para domingo, agrupamos tres subcolumnas -->
                                                            <th colspan="3">{{ "{$date['fecha']} - {$date['dia_semana_lit']}" }}</th>
                                                        @else
                                                            <th>{{ "{$date['fecha']} - {$date['dia_semana_lit']}" }}</th>
                                                        @endif
                                                    @endforeach
                                                    <th>Total Multas</th>
                                                    <th>Observaciones</th>
                                                </tr>
                                                <tr>
                                                    {{-- En este segundo renglón definimos los subencabezados para domingos --}}
                                                    <th colspan="3"></th>
                                                    @foreach ($dates as $date)
                                                        @if ($date['dia_semana'] == '0')
                                                            <th>1ra</th>
                                                            <th>2da</th>
                                                            <th>3ra</th>
                                                        @else
                                                            <th></th>
                                                        @endif
                                                    @endforeach
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($multas_detalle_reporte as $empleado)
                                                    <tr>
                                                        <td>{{ $empleado->emp_firstname }}</td>
                                                        <td>{{ $empleado->emp_lastname }}</td>
                                                        <td>{{ $empleado->dept_name }}</td>
                                                        {{-- Se muestran las multas según el día --}}
                                                        @foreach ($dates as $date)
                                                            @if ($date['dia_semana'] == '0')
                                                                <td>{{ $empleado->{ $date['alias'] . '_1' } ?? 0 }}</td>
                                                                <td>{{ $empleado->{ $date['alias'] . '_2' } ?? 0 }}</td>
                                                                <td>{{ $empleado->{ $date['alias'] . '_3' } ?? 0 }}</td>
                                                            @else
                                                                <td>{{ $empleado->{$date['alias']} ?? 0 }}</td>
                                                            @endif
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


