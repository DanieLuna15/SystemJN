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
                            class="table table-striped table-bordered table-hover table-sm datatable">
                            <thead>
                                <tr>
                                    <th style="text-align: center">Día de la Semana</th>
                                    <th style="text-align: center">Actividad o Servicio</th>
                                    <th style="text-align: center">Hora de Inicio</th>
                                    <th style="text-align: center">Hora Multa</th>
                                    <th style="text-align: center">Categoría</th>
                                    <th style="text-align: center">Estado</th>
                                    <th style="text-align: center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($horarios as $horario)
                                    <tr>
                                        <td class="text-center">{{ $horario->dia_semana_texto }}</td>
                                        <td class="text-center">{{ $horario->actividadServicio->nombre }}</td>
                                        <td class="text-center">{{ $horario->hora_registro }}</td>
                                        <td class="text-center">{{ $horario->hora_multa }}</td>
                                        <td class="text-center align-middle">
                                            @if ($horario->tipo == 1)
                                                <small class="badge bg-gradient-primary w-100 h-100"><i
                                                        class="fas fa-lock"></i> Fijo</small>
                                            @else
                                                <small class="badge bg-gradient-info w-100 h-100"><i
                                                        class="far fa-clock"></i> Eventual</small>
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            {!! $horario->statusBadge !!}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.horarios.edit', $horario) }}"
                                                class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" title="Cambiar estado"
                                                class="btn btn-sm {{ $horario->estado ? 'btn-danger' : 'btn-success' }} confirmationBtn"
                                                data-action="{{ route('admin.horarios.status', $horario->id) }}"
                                                data-question="{{ $horario->estado ? '¿Seguro que deseas inhabilitar el Horario del <strong>' . $horario->dia_semana_texto . '</strong>?' : '¿Seguro que deseas habilitar el Horario del <strong>' . $horario->dia_semana_texto . '</strong>?' }}">
                                                <i class="fas {{ $horario->estado ? 'fa-eye-slash' : 'fa-eye' }}"></i>
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
