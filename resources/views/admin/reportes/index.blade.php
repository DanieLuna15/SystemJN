@extends('adminlte::page')

@section('title', $pageTitle)

{{-- Push extra CSS --}}

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-body">
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

    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-body">
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
    </div>
@stop
@push('css')
@endpush

{{-- Push extra scripts --}}

@push('js')
@endpush

@push('breadcrumb-plugins')
@endpush
