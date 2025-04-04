@extends('adminlte::page')

@section('title', $pageTitle)

{{-- Contenido --}}
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="horarios-table"
                            class="table table-striped table-bordered table-hover table-sm datatable text-center">
                            <thead>
                                <tr>
                                    <th style="text-align: center">Ministerio(s)</th>
                                    <th style="text-align: center">Autor</th>
                                    <th style="text-align: center">Fecha</th>
                                    <th style="text-align: center">Hora de Inicio</th>
                                    <th style="text-align: center">Hora Fin</th>
                                    <th style="text-align: center">Motivo</th>
                                    <th style="text-align: center">Tiempo</th>
                                    <th style="text-align: center">Estado</th>
                                    <th class="no-export" style="text-align: center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($excepciones as $excepcion)
                                    <tr>
                                        <!-- Ministerios centrado -->
                                        <td class="align-middle text-truncate">
                                            @foreach ($excepcion->ministerios as $ministerio)
                                                [<span class="badge badge-info">{{ $ministerio->nombre }}</span>]
                                            @endforeach
                                        </td>

                                        <!-- Usuario Autor centrado -->
                                        <td class="text-center align-middle">
                                            @if ($excepcion->usuario == null)
                                                <small class="badge bg-gradient-warning w-100 h-100">
                                                    Sin datos
                                                </small>
                                            @else
                                                {{ $excepcion->usuario->name }}
                                            @endif
                                        </td>


                                        <!-- fecha centrado -->
                                        <td class="text-center align-middle">
                                            @if ($excepcion->fecha == null)
                                                <small class="badge bg-gradient-warning w-100 h-100">
                                                    Sin datos
                                                </small>
                                            @else
                                                {{ $excepcion->fecha }}
                                            @endif
                                        </td>

                                        <!-- Hora de Inicio centrada -->
                                        <td class="text-center align-middle">
                                            @if ($excepcion->hora_inicio == null)
                                                <small class="badge bg-gradient-warning w-100 h-100">
                                                    Sin datos
                                                </small>
                                            @else
                                                <small class="badge bg-gradient-primary w-100 h-100">
                                                    <i class="far fa-clock"></i> {{ $excepcion->hora_inicio }}
                                                </small>
                                            @endif
                                        </td>

                                        <!-- Hora Fin centrada -->
                                        <td class="text-center align-middle">
                                            @if ($excepcion->hora_fin == null)
                                                <small class="badge bg-gradient-warning w-100 h-100">
                                                    Sin datos
                                                </small>
                                            @else
                                                <small class="badge bg-gradient-primary w-100 h-100">
                                                    <i class="far fa-clock"></i> {{ $excepcion->hora_fin }}
                                                </small>
                                            @endif
                                        </td>

                                        <!-- Motivo centrada -->
                                        <td class="text-center">{{ $excepcion->motivo }}</td>

                                        <!-- Tiempo excepcion centrada -->
                                        <td class="text-center align-middle">
                                            @switch($excepcion->dia_entero)
                                                @case(1)
                                                    <small class="badge bg-gradient-primary w-100 h-100">
                                                        <i class="fas fa-sun"></i> Todo el día
                                                    </small>
                                                    @break
                                                @case(0)
                                                    <small class="badge bg-gradient-info w-100 h-100">
                                                        <i class="far fa-clock"></i> Rango de horas
                                                    </small>
                                                    @break
                                                @case(2)
                                                    <small class="badge bg-gradient-warning w-100 h-100">
                                                        <i class="fas fa-calendar-alt"></i> Varios días
                                                    </small>
                                                    @break
                                            @endswitch
                                        </td>

                                        <!-- Estado centrado -->
                                        <td class="text-center align-middle">
                                            {!! $excepcion->statusBadge !!}
                                        </td>

                                        <!-- Acciones centradas -->
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                @can('editar excepciones')
                                                    <a href="{{ route('admin.excepciones.edit', $excepcion) }}"
                                                        class="btn btn-warning btn-sm mx-1" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('cambiar estado excepciones')
                                                    <button type="button" title="Cambiar estado"
                                                        class="btn btn-sm {{ $excepcion->estado ? 'btn-danger' : 'btn-success' }} confirmationBtn mx-1"
                                                        data-action="{{ route('admin.excepciones.status', $excepcion->id) }}"
                                                        data-question="{{ $excepcion->estado ? '¿Seguro que deseas inhabilitar la Excepcion del <strong>' . $excepcion->motivo . '</strong>?' : '¿Seguro que deseas habilitar la Excepcion del <strong>' . $excepcion->motivo . '</strong>?' }}">
                                                        <i class="fas {{ $excepcion->estado ? 'fa-eye-slash' : 'fa-eye' }}"></i>
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
{{-- Push extra styles --}}
@push('css')
@endpush

{{-- Push extra scripts --}}
@push('js')
    <script></script>
@endpush

@push('breadcrumb-plugins')
    @can('crear excepciones')
        <a href="{{ route('admin.excepciones.create') }}" class="btn btn-success rounded">
            <i class="fas fa-plus-square"></i> Nuevo
        </a>
    @endcan
@endpush
