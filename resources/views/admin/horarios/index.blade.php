@extends('adminlte::page')

@section('title', 'Horarios')

@section('content_header')
    <h1><b>Gestión de Horarios</b></h1>
@stop

{{-- Contenido --}}
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <div class="card-title">Listado de Horarios</div>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <a href="{{ route('admin.horarios.create') }}" class="btn btn-success"> + Agregar Horario</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="horarios-table"
                            class="table table-striped table-bordered table-hover table-sm datatable">
                            <thead>
                                <tr>
                                    <th style="text-align: center">Ministerio</th>
                                    <th style="text-align: center">Día de la Semana</th>
                                    <th style="text-align: center">Hora de Inicio</th>
                                    <th style="text-align: center">Hora Multa</th>
                                    <th style="text-align: center">Estado</th>
                                    <th style="text-align: center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($horarios as $horario)
                                    <tr>
                                        <td class="text-center">{{ $horario->ministerio->nombre }}</td>
                                        <td class="text-center">{{ $horario->dia_semana_texto }}</td>
                                        <td class="text-center">{{ $horario->hora_registro }}</td>
                                        <td class="text-center">{{ $horario->hora_multa }}</td>

                                        <td class="text-center align-middle">
                                            <div class="d-flex justify-content-center">
                                                {!! $horario->statusBadge !!}
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            <a href="{{ route('admin.horarios.edit', $horario) }}"
                                                class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <button type="button"
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

@push('css')
@endpush

{{-- Push extra scripts --}}
@push('js')
    <script></script>
@endpush
