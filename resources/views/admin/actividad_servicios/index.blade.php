@extends('adminlte::page')

@section('title', $pageTitle)

{{-- Push extra CSS --}}

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <div class="card-title">Listado de Actividades y Servicios</div>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>

                        <a href="{{ route('admin.actividad_servicios.create') }}" class="btn btn-success"> + Agregar
                            Actividad o Servicio</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="actividad_servicios-table"
                            class="table table-striped table-bordered table-hover table-sm datatable">
                            <thead>
                                <tr>
                                    <th style="text-align: center">Imagen</th>
                                    <th style="text-align: center">Nombre</th>
                                    <th style="text-align: center">Estado</th>
                                    <th style="text-align: center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($actividad_servicios as $actividad_servicio)
                                    <tr>
                                        <td class="text-center">
                                            @if ($actividad_servicio->imagen)
                                                <img src="{{ asset($actividad_servicio->imagen) }}" alt="Logo" width="50">
                                            @else
                                                <span class="text-muted font-italic">Sin imagen</span>
                                            @endif
                                        </td>
                                        <td>{{ $actividad_servicio->nombre }}</td>

                                        <td class="text-center align-middle">
                                            <div class="d-flex justify-content-center">
                                                {!! $actividad_servicio->statusBadge !!}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.actividad_servicios.edit', $actividad_servicio) }}"
                                                class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" title="Cambiar estado"
                                                class="btn btn-sm {{ $actividad_servicio->estado ? 'btn-danger' : 'btn-success' }} confirmationBtn"
                                                data-action="{{ route('admin.actividad_servicios.status', $actividad_servicio->id) }}"
                                                data-question="{{ $actividad_servicio->estado ? '¿Seguro que deseas inhabilitar la Actividad o Servicio <strong>' . $actividad_servicio->nombre . '</strong>?' : '¿Seguro que deseas habilitar la Actividad o Servicio <strong>' . $actividad_servicio->nombre . '</strong>?' }}">
                                                <i class="fas {{ $actividad_servicio->estado ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                            </button>

                                        </td>
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
    <script></script>
@endpush
