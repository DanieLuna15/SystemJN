@extends('adminlte::page')

@section('title', $pageTitle)

{{-- Contenido --}}
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="permisos-table"
                            class="table table-striped table-bordered table-hover table-sm datatable text-center">
                            <thead>
                                <tr>
                                    <th style="text-align: center">Usuario(s)</th>
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
                                @foreach ($permisos as $permiso)
                                    <tr>
                                        <!-- Usuarios centrado -->
                                        <td class="align-middle text-truncate">
                                            @foreach ($permiso->usuarios as $usuario)
                                                [<span class="badge badge-info">{{ $usuario->name }} {{ $usuario->last_name }}</span>]
                                            @endforeach
                                        </td>

                                        <!-- Usuario Autor centrado -->
                                        <td class="text-center align-middle">
                                            @if ($permiso->usuario == null)
                                                <small class="badge bg-gradient-warning w-100 h-100">
                                                    Sin datos
                                                </small>
                                            @else
                                                {{ $permiso->usuario->name }} 
                                            @endif
                                        </td>
                                        
                                        <!-- Fecha centrada -->
                                        <td class="text-center align-middle">
                                            @if ($permiso->fecha == null)
                                                <small class="badge bg-gradient-warning w-100 h-100">
                                                    Sin datos
                                                </small>
                                            @else
                                                {{ $permiso->fecha }}
                                            @endif
                                        </td>

                                        <!-- Hora de Inicio centrada -->
                                        <td class="text-center align-middle">
                                            @if ($permiso->hora_inicio == null)
                                                <small class="badge bg-gradient-warning w-100 h-100">
                                                    Sin datos
                                                </small>
                                            @else
                                                <small class="badge bg-gradient-primary w-100 h-100">
                                                    <i class="far fa-clock"></i> {{ $permiso->hora_inicio }}
                                                </small>
                                            @endif
                                        </td>

                                        <!-- Hora Fin centrada -->
                                        <td class="text-center align-middle">
                                            @if ($permiso->hora_fin == null)
                                                <small class="badge bg-gradient-warning w-100 h-100">
                                                    Sin datos
                                                </small>
                                            @else
                                                <small class="badge bg-gradient-primary w-100 h-100">
                                                    <i class="far fa-clock"></i> {{ $permiso->hora_fin }}
                                                </small>
                                            @endif
                                        </td>

                                        <!-- Motivo centrada -->
                                        <td class="text-center">{{ $permiso->motivo }}</td>

                                        <!-- Tipo de permiso centrado -->
                                        <td class="text-center align-middle">
                                            @switch($permiso->dia_entero)
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
                                            {!! $permiso->statusBadge !!}
                                        </td>

                                        <!-- Acciones centradas -->
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                @can('editar permisos')
                                                    <a href="{{ route('admin.permisos.edit', $permiso) }}"
                                                        class="btn btn-warning btn-sm mx-1" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('cambiar estado permisos')
                                                @php
                                                    // Definir clases de estado y mensajes de pregunta en un array
                                                    $clasesEstado = [
                                                        0 => 'btn-warning',
                                                        1 => 'btn-success',
                                                        2 => 'btn-danger'
                                                    ];
                                            
                                                    $iconosEstado = [
                                                        0 => 'fa-hourglass-start',
                                                        1 => 'fa-check-circle',
                                                        2 => 'fa-times-circle'
                                                    ];

                                                    // Obtener nombres de los usuarios involucrados
                                                    $usuariosInvolucrados = $permiso->usuarios->pluck('name')->implode(', ');
                                            
                                                    $preguntasEstado = [
                                                        0 => "¿Seguro que deseas autorizar el Permiso de <strong>{$usuariosInvolucrados}</strong>?",
                                                        1 => "¿Seguro que deseas rechazar el Permiso de <strong>{$usuariosInvolucrados}</strong>?",
                                                        2 => "¿Seguro que deseas volver a pendiente el Permiso de <strong>{$usuariosInvolucrados}</strong>?"
                                                    ];
                                                @endphp
                                            
                                                <button type="button" title="Cambiar estado"
                                                        class="btn btn-sm {{ $clasesEstado[$permiso->estado] }} confirmationBtn mx-1"
                                                        data-action="{{ route('admin.permisos.status', $permiso->id) }}"
                                                        data-question="{{ $preguntasEstado[$permiso->estado] }}">
                                                    <i class="fas {{ $iconosEstado[$permiso->estado] }}"></i>
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
@push('breadcrumb-plugins')
    @can('crear permisos')
        <a href="{{ route('admin.permisos.create') }}" class="btn btn-success rounded">
            <i class="fas fa-plus-square"></i> Nuevo
        </a>
    @endcan
@endpush
