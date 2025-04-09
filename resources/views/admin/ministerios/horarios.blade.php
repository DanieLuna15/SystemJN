@extends('adminlte::page')

@section('title', $pageTitle)

{{-- Contenido --}}
@section('content')


    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link active" href="#horarios" data-toggle="tab" data-section="horarios">Horarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#excepciones" data-toggle="tab" data-section="excepciones">Excepciones</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="horarios">
                        <div class="tab-pane active" id="horarios">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table id="horarios-table"
                                                class="table table-striped table-bordered table-hover table-sm datatable text-center">
                                                <thead>
                                                    <tr>
                                                        <th style="text-align: center">Día de la Semana</th>
                                                        <th style="text-align: center">Fecha</th>
                                                        <th style="text-align: center">Actividad o Servicio</th>
                                                        <th style="text-align: center">Hora de Inicio</th>
                                                        <th style="text-align: center">Hora Multa</th>
                                                        <th style="text-align: center">Tipo</th>
                                                        <th style="text-align: center">Estado</th>
                                                        <th style="text-align: center">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($horarios as $horario)
                                                        <tr>
                                                            <!-- Día de la Semana centrado -->
                                                            <td class="text-center align-middle">
                                                                @if ($horario->dia_semana == null)
                                                                    <small class="badge bg-gradient-warning w-100 h-100">
                                                                        Sin datos
                                                                    </small>
                                                                @else
                                                                    {{ $horario->dia_semana_texto }}
                                                                @endif
                                                            </td>

                                                            <!-- Fecha centrado -->
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
                                                                {{ __(strLimit($horario->actividadServicio->nombre, 20)) }}
                                                            </td>

                                                            <!-- Hora de Inicio centrada -->
                                                            <td class="text-center">{{ $horario->hora_registro }}</td>

                                                            <!-- Hora Multa centrada -->
                                                            <td class="text-center">{{ $horario->hora_multa }}</td>

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
                                                                    @can('cambiar estado horarios')
                                                                        <button type="button" title="Cambiar estado"
                                                                            class="btn btn-sm {{ $horario->estado ? 'btn-danger' : 'btn-success' }} confirmationBtn mx-1"
                                                                            data-action="{{ route('admin.horarios.status', $horario->id) }}"
                                                                            data-question="{{ $horario->estado ? '¿Seguro que deseas inhabilitar el Horario del <strong>' . $horario->dia_semana_texto . '</strong>?' : '¿Seguro que deseas habilitar el Horario del <strong>' . $horario->dia_semana_texto . '</strong>?' }}">
                                                                            <i
                                                                                class="fas {{ $horario->estado ? 'fa-eye-slash' : 'fa-eye' }}"></i>
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
                    </div>
                    <div class="tab-pane" id="excepciones">
                        <div class="tab-pane" id="excepciones">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table id="horarios-table"
                                                class="table table-striped table-bordered table-hover table-sm datatable text-center">
                                                <thead>
                                                    <tr>
                                                        <th style="text-align: center">Autor</th>
                                                        <th style="text-align: center">Fecha</th>
                                                        <th style="text-align: center">Fecha Fin</th>
                                                        <th style="text-align: center">Hora de Inicio</th>
                                                        <th style="text-align: center">Hora Fin</th>
                                                        <th style="text-align: center">Motivo</th>
                                                        <th style="text-align: center">Tiempo</th>
                                                        <th style="text-align: center">Estado</th>
                                                        <th style="text-align: center">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($excepciones as $excepcion)
                                                        <tr>
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

                                                            <!-- Fecha centrada -->
                                                            <td class="text-center align-middle">
                                                                @if ($excepcion->fecha == null)
                                                                    <small class="badge bg-gradient-warning w-100 h-100">
                                                                        Sin datos
                                                                    </small>
                                                                @else
                                                                    {{ $excepcion->fecha }}
                                                                @endif
                                                            </td>

                                                            <!-- Hasta -->
                                                            <td class="text-center align-middle">
                                                                @if ($excepcion->hasta == null)
                                                                    <small class="badge bg-gradient-warning w-100 h-100">
                                                                        Sin datos
                                                                    </small>
                                                                @else
                                                                    {{ $excepcion->hasta }}
                                                                @endif
                                                            </td>

                                                            <!-- Hora de Inicio -->
                                                            <td class="text-center align-middle">
                                                                @if ($excepcion->hora_inicio == null)
                                                                    <small class="badge bg-gradient-warning w-100 h-100">
                                                                        Sin datos
                                                                    </small>
                                                                @else
                                                                    <small class="badge bg-gradient-primary w-100 h-100">
                                                                        <i class="far fa-clock"></i>
                                                                        {{ $excepcion->hora_inicio }}
                                                                    </small>
                                                                @endif
                                                            </td>

                                                            <!-- Hora Fin -->
                                                            <td class="text-center align-middle">
                                                                @if ($excepcion->hora_fin == null)
                                                                    <small class="badge bg-gradient-warning w-100 h-100">
                                                                        Sin datos
                                                                    </small>
                                                                @else
                                                                    <small class="badge bg-gradient-primary w-100 h-100">
                                                                        <i class="far fa-clock"></i>
                                                                        {{ $excepcion->hora_fin }}
                                                                    </small>
                                                                @endif
                                                            </td>

                                                            <!-- Motivo -->
                                                            <td class="text-center">{{ $excepcion->motivo }}</td>

                                                            <!-- Tipo de excepción -->
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

                                                            <!-- Estado -->
                                                            <td class="text-center align-middle">
                                                                {!! $excepcion->statusBadge !!}
                                                            </td>

                                                             <!-- Acciones centradas -->
                                                             <td class="text-center">
                                                                <div class="d-flex justify-content-center">
                                                                    @can('cambiar estado excepciones')
                                                                        <button type="button" title="Cambiar estado"
                                                                            class="btn btn-sm {{ $excepcion->estado ? 'btn-danger' : 'btn-success' }} confirmationBtn mx-1"
                                                                            data-action="{{ route('admin.excepciones.status', $excepcion->id) }}"
                                                                            data-question="{{ $excepcion->estado ? '¿Seguro que deseas inhabilitar la excepción del <strong>' . $excepcion->fecha . '</strong>?' : '¿Seguro que deseas habilitar la excepción del <strong>' . $excepcion->fecha . '</strong>?' }}">
                                                                            <i
                                                                                class="fas {{ $excepcion->estado ? 'fa-eye-slash' : 'fa-eye' }}"></i>
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
    {{-- @can('crear horarios')
        <a href="{{ route('admin.horarios.create') }}" class="btn btn-success rounded d-flex align-items-center me-2">
            <i class="fas fa-plus-square me-1"></i>
            <span>Nuevo</span>
        </a>
    @endcan --}}

    <a href="{{ route('admin.ministerios.index') }}" class="btn btn-secondary rounded d-flex align-items-center mx-2">
        <i class="fas fa-undo me-1"></i>
        <span>Regresar</span>
    </a>
@endpush
