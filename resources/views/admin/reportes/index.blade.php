@extends('adminlte::page')

@section('title', $pageTitle)

{{-- Push extra CSS --}}

@section('content')

    <x-adminlte-card>
        <form action="{{-- {{ route('admin.reportes.index') }} --}}" method="POST">
            @csrf
            <div class="row">
                <!-- Campo Fecha (Oculto por defecto) -->
                {{-- Minimal --}}
                {{-- <x-adminlte-date-range name="drBasic" /> --}}

                {{-- Disabled with predefined config --}}
                {{-- @php
                    $config = [
                        'timePicker' => true,
                        'startDate' => "js:moment().subtract(6, 'days')",
                        'endDate' => 'js:moment()',
                        'locale' => ['format' => 'YYYY-MM-DD HH:mm'],
                    ];
                @endphp
                <x-adminlte-date-range name="drDisabled" :config="$config" disabled /> --}}

                {{-- Prepend slot and custom ranges enables --}}
                {{-- <x-adminlte-date-range name="drCustomRanges" enable-default-ranges="Last 30 Days">
                    <x-slot name="prependSlot">
                        <div class="input-group-text bg-gradient-info">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </x-slot>
                </x-adminlte-date-range> --}}

                {{-- Label and placeholder --}}
                {{-- <x-adminlte-date-range name="drPlaceholder" placeholder="Select a date range..." label="Date Range">
                    <x-slot name="prependSlot">
                        <div class="input-group-text bg-gradient-danger">
                            <i class="far fa-lg fa-calendar-alt"></i>
                        </div>
                    </x-slot>
                </x-adminlte-date-range>
                @push('js')
                    <script>
                        $(() => $("#drPlaceholder").val(''))
                    </script>
                @endpush --}}

                {{-- SM size with single date/time config --}}
                {{-- @php
                    $config = [
                        'singleDatePicker' => true,
                        'showDropdowns' => true,
                        'startDate' => 'js:moment()',
                        'minYear' => 2000,
                        'maxYear' => "js:parseInt(moment().format('YYYY'),10)",
                        'timePicker' => true,
                        'timePicker24Hour' => true,
                        'timePickerSeconds' => true,
                        'cancelButtonClasses' => 'btn-danger',
                        'locale' => ['format' => 'YYYY-MM-DD HH:mm:ss'],
                    ];
                @endphp
                <x-adminlte-date-range name="drSizeSm" label="Date/Time" igroup-size="sm" :config="$config">
                    <x-slot name="appendSlot">
                        <div class="input-group-text bg-dark">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </x-slot>
                </x-adminlte-date-range> --}}

                {{-- LG size with some config and add-ons --}}
                @php
                    $config = [
                        'showDropdowns' => true,
                        'startDate' => 'js:moment()',
                        'endDate' => "js:moment().subtract(1, 'days')",
                        'minYear' => 2000,
                        'maxYear' => "js:parseInt(moment().format('YYYY'),10)",
                        'timePicker' => true,
                        'timePicker24Hour' => true,
                        'timePickerIncrement' => 30,
                        'locale' => ['format' => 'YYYY-MM-DD HH:mm:ss'],
                        'opens' => 'center',
                    ];
                @endphp

                <div class="col-md-6 col-lg-6">
                    <x-adminlte-date-range name="drSizeLg" label="Date/Time Range" :config="$config">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="far fa-calendar-alt"></i>
                            </div>
                        </x-slot>

                    </x-adminlte-date-range>
                </div>


                <div class="col-md-6 col-lg-6">
                    <div class="form-group">
                        <label>Ministerio:</label>
                        <x-adminlte-select2 name="ministerio_id" class="form-control">
                            <option value="" selected disabled>Seleccione un ministerio</option>
                            @foreach ($ministerios as $ministerio)
                                <option value="{{ $ministerio->id }}"
                                    {{ old('ministerio_id', '') == $ministerio->id ? 'selected' : '' }}>
                                    {{ $ministerio->dept_name }}
                                </option>
                            @endforeach
                        </x-adminlte-select2>
                    </div>
                </div>
            </div>

            {{-- Minimal --}}
            <x-adminlte-select2 name="sel2Basic">
                <option>Option 1</option>
                <option disabled>Option 2</option>
                <option selected>Option 3</option>
            </x-adminlte-select2>

            <!-- Botones de Acción -->
            <div class="d-flex justify-content-between">
                <x-adminlte-button class="btn w-100" type="submit" label="Consultar" theme="success"
                    icon="fas fa-lg fa-save" />
            </div>
        </form>
    </x-adminlte-card>


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
