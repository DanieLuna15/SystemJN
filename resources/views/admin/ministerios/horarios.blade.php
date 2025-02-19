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
                            <thead class="text-center">
                                <tr>
                                    <th>Día de la Semana</th>
                                    <th>Actividad o Servicio</th>
                                    <th>Hora de Inicio</th>
                                    <th>Hora Multa</th>
                                    <th>Categoría</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($horarios as $horario)
                                    <tr>
                                        <!-- Día de la Semana centrado -->
                                        <td >{{ $horario->dia_semana_texto }}</td>

                                        <!-- Actividad o Servicio centrado -->
                                        <td class="align-middle text-truncate">{{ __(strLimit($horario->actividadServicio->nombre, 20)) }}</td>

                                        <!-- Hora de Inicio centrada -->
                                        <td class="align-middle">{{ $horario->hora_registro }}</td>

                                        <!-- Hora Multa centrada -->
                                        <td class="align-middle">{{ $horario->hora_multa }}</td>

                                        <!-- Categoría centrada -->
                                        <td class="align-middle">
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
                                        <td class="align-middle">
                                            {!! $horario->statusBadge !!}
                                        </td>

                                        <!-- Acciones centradas -->
                                        <td class="align-middle">
                                            <div class="d-flex justify-content-center">
                                                <a href="{{ route('admin.horarios.edit', $horario) }}"
                                                    class="btn btn-warning btn-sm mx-1" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" title="Cambiar estado"
                                                    class="btn btn-sm {{ $horario->estado ? 'btn-danger' : 'btn-success' }} confirmationBtn mx-1"
                                                    data-action="{{ route('admin.horarios.status', $horario->id) }}"
                                                    data-question="{{ $horario->estado ? '¿Seguro que deseas inhabilitar el Horario del <strong>' . $horario->dia_semana_texto . '</strong>?' : '¿Seguro que deseas habilitar el Horario del <strong>' . $horario->dia_semana_texto . '</strong>?' }}">
                                                    <i class="fas {{ $horario->estado ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                                </button>
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
    <a href="{{ route('admin.horarios.create') }}" class="btn btn-success rounded">
        <i class="fas fa-plus-square"></i> Nuevo
    </a>
@endpush
