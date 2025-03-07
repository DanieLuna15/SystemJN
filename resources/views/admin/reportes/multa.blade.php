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
                            <a class="nav-link" href="#detallado" data-toggle="tab" data-section="logotipos">Detallado</a>
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
