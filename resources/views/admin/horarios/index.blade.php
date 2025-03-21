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
                                    <th style="text-align: center">Día de la Semana</th>
                                    <th style="text-align: center">Fecha</th>
                                    <th style="text-align: center">Actividad o Servicio</th>
                                    <th style="text-align: center">Hora de Inicio</th>
                                    <th style="text-align: center">Hora Multa</th>
                                    <th style="text-align: center">Hora Límite</th>
                                    <th style="text-align: center">Tipo</th>
                                    <th style="text-align: center">Estado</th>
                                    <th class="no-export" style="text-align: center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($horarios as $horario)
                                    <tr>
                                        <!-- Ministerios centrado -->
                                        <td class="align-middle text-truncate">
                                            @foreach ($horario->ministerios as $ministerio)
                                                [<span class="badge badge-info">{{ $ministerio->nombre }}</span>]
                                            @endforeach
                                        </td>

                                        <!-- Día de la Semana centrado -->
                                        <td class="text-center align-middle">
                                            @if ($horario->dia_semana == '')
                                                <small class="badge bg-gradient-warning w-100 h-100">
                                                    Sin datos
                                                </small>
                                            @else
                                                {{ $horario->dia_semana_texto }}
                                            @endif
                                        </td>

                                        <!-- fecha centrado -->

                                        <td class="text-center align-middle">
                                            @if ($horario->fecha == null)
                                                <small class="badge bg-gradient-warning w-100 h-100">
                                                    Sin datos
                                                </small>
                                            @else
                                                {{ $horario->fecha }}
                                            @endif
                                        </td>

                                        <!-- Actividad o Servicio centrado -->
                                        <td class="align-middle text-truncate">
                                            {{ __(strLimit($horario->actividadServicio->nombre, 20)) }}</td>

                                        <!-- Hora de Inicio centrada -->
                                        <td class="text-center">{{ $horario->hora_registro }}</td>

                                        <!-- Hora Multa centrada -->
                                        <td class="text-center">{{ $horario->hora_multa }}</td>

                                        <!-- Hora Limite centrada -->
                                        <td class="text-center">{{ $horario->hora_limite }}</td>


                                        <!-- Tipo centrado -->
                                        <td class="text-center align-middle">
                                            @if ($horario->tipo == 1)
                                                <small class="badge bg-gradient-primary w-100 h-100">
                                                    <i class="fas fa-lock"></i> Fijo
                                                </small>
                                            @else
                                                <small class="badge bg-gradient-info w-100 h-100">
                                                    <i class="far fa-clock"></i> Eventual
                                                </small>
                                            @endif
                                        </td>

                                        <!-- Estado centrado -->
                                        <td class="text-center align-middle">
                                            {!! $horario->statusBadge !!}
                                        </td>

                                        <!-- Acciones centradas -->
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                @can('editar horarios')
                                                    <a href="{{ route('admin.horarios.edit', $horario) }}"
                                                        class="btn btn-warning btn-sm mx-1" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('cambiar estado horarios')
                                                    <button type="button" title="Cambiar estado"
                                                        class="btn btn-sm {{ $horario->estado ? 'btn-danger' : 'btn-success' }} confirmationBtn mx-1"
                                                        data-action="{{ route('admin.horarios.status', $horario->id) }}"
                                                        data-question="{{ $horario->estado ? '¿Seguro que deseas inhabilitar el Horario del <strong>' . $horario->dia_semana_texto . '</strong>?' : '¿Seguro que deseas habilitar el Horario del <strong>' . $horario->dia_semana_texto . '</strong>?' }}">
                                                        <i class="fas {{ $horario->estado ? 'fa-eye-slash' : 'fa-eye' }}"></i>
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
    @can('crear horarios')
        <a href="{{ route('admin.horarios.create') }}" class="btn btn-success rounded">
            <i class="fas fa-plus-square"></i> Nuevo
        </a>
    @endcan
@endpush
