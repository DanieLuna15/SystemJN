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
                                    <th>Imagen</th>
                                    <th>Nombre</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($actividad_servicios as $actividad_servicio)
                                    <tr>
                                        <!-- Imagen centrada -->
                                        <td class="align-middle">
                                            @if ($actividad_servicio->imagen)
                                                <img src="{{ asset($actividad_servicio->imagen) }}"
                                                    title="Imagen referencial" class="img-rounded">
                                            @else
                                                <img src="{{ asset('images/default-dark.png') }}" title="Sin imagen"
                                                    class="img-rounded">
                                            @endif
                                        </td>

                                        <!-- Nombre centrado -->
                                        <td class="align-middle text-truncate" style="max-width: 150px;">
                                            {{ __(strLimit($actividad_servicio->nombre, 30)) }}
                                        </td>

                                        <!-- Estado centrado -->
                                        <td class="align-middle">
                                            <div class="d-flex justify-content-center">
                                                {!! $actividad_servicio->statusBadge !!}
                                            </div>
                                        </td>
                                        <!-- Acciones centradas -->
                                        <td class="align-middle">
                                            <div class="d-flex justify-content-center">
                                                @can('editar actividades_servicios')
                                                    <a href="{{ route('admin.actividad_servicios.edit', $actividad_servicio) }}"
                                                        class="btn btn-warning btn-sm mx-1" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('cambiar estado actividades_servicios')
                                                    <button type="button" title="Cambiar estado"
                                                        class="btn btn-sm {{ $actividad_servicio->estado ? 'btn-danger' : 'btn-success' }} confirmationBtn mx-1"
                                                        data-action="{{ route('admin.actividad_servicios.status', $actividad_servicio->id) }}"
                                                        data-question="{{ $actividad_servicio->estado ? '¿Seguro que deseas inhabilitar la Actividad o Servicio <strong>' . $actividad_servicio->nombre . '</strong>?' : '¿Seguro que deseas habilitar la Actividad o Servicio <strong>' . $actividad_servicio->nombre . '</strong>?' }}">
                                                        <i
                                                            class="fas {{ $actividad_servicio->estado ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                                    </button>
                                                @endcan
                                            </div>
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
@endpush

@push('breadcrumb-plugins')
    @can('crear actividades_servicios')
        <a href="{{ route('admin.actividad_servicios.create') }}" class="btn btn-success rounded">
            <i class="fas fa-plus-square"></i> Nuevo
        </a>
    @endcan
@endpush
