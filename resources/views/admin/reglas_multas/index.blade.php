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
                                    <th style="text-align: center">Multa por fata</th>
                                    <th style="text-align: center">Multa incremental</th>
                                    <th style="text-align: center">Multa por retraso largo</th>
                                    <th style="text-align: center">Minutos por incremento</th>
                                    <th style="text-align: center">Minutos por retraso largo</th>
                                    <th style="text-align: center">Descripcion</th>
                                    <th style="text-align: center">Estado</th>
                                    <th class="no-export" style="text-align: center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reglas_multas as $regla_multa)
                                    <tr>
                                        <!-- Ministerios centrado -->
                                        <td class="align-middle text-truncate">
                                            @foreach ($regla_multa->ministerios as $ministerio)
                                                [<span class="badge badge-info">{{ $ministerio->nombre }}</span>]
                                            @endforeach
                                        </td>

                                        <!-- Multa por falta centrada -->
                                        <td class="text-center">{{ $regla_multa->multa_por_falta }}</td>

                                        <!-- Multa incremental centrada -->
                                        <td class="text-center">{{ $regla_multa->multa_incremental }}</td>

                                        <!-- Multa por retraso largo centrada -->
                                        <td class="text-center">{{ $regla_multa->multa_por_retraso_largo }}</td>

                                        <!-- Minutos por incremento centrada -->
                                        <td class="text-center">{{ $regla_multa->minutos_por_incremento }}</td>

                                        <!-- Minutos por retraso largo centrada -->
                                        <td class="text-center">{{ $regla_multa->minutos_retraso_largo }}</td>

                                        <!-- descripcion centrada -->
                                        <td class="text-center">{{ $regla_multa->descripcion }}</td>

                                        <!-- Estado centrado -->
                                        <td class="text-center align-middle">
                                            {!! $regla_multa->statusBadge !!}
                                        </td>

                                        <!-- Acciones centradas -->
                                        <td class="align-middle">
                                            <div class="d-flex justify-content-center">
                                                @can('editar horarios')
                                                    <a href="{{ route('admin.reglas_multas.edit', $regla_multa) }}"
                                                        class="btn btn-warning btn-sm mx-1" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('cambiar estado horarios')
                                                    <button type="button" title="Cambiar estado"
                                                        class="btn btn-sm {{ $regla_multa->estado ? 'btn-danger' : 'btn-success' }} confirmationBtn mx-1"
                                                        data-action="{{ route('admin.horarios.status', $regla_multa->id) }}"
                                                        data-question="{{ $regla_multa->estado ? '¿Seguro que deseas inhabilitar la Regla del <strong>' . $regla_multa->descripcion . '</strong>?' : '¿Seguro que deseas habilitar la Regla del <strong>' . $regla_multa->descripcion . '</strong>?' }}">
                                                        <i class="fas {{ $regla_multa->estado ? 'fa-eye-slash' : 'fa-eye' }}"></i>
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
        <a href="{{ route('admin.reglas_multas.create') }}" class="btn btn-success rounded">
            <i class="fas fa-plus-square"></i> Nuevo
        </a>
    @endcan
@endpush
