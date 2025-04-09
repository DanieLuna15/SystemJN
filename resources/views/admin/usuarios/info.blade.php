@extends('adminlte::page')
@section('title', $pageTitle)

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile text-center">
                    <!-- Imagen de perfil -->
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle"
                            src="{{ asset($usuario->profile_image ?? 'images/default-dark.png') }}" alt="Foto de perfil">
                    </div>
                    <!-- Datos del usuario -->
                    <ul class="list-group list-group-unbordered mb-3">
                        <!-- Nombre completo -->
                        <li class="list-group-item text-center">
                            <b><i class="fas fa-user"></i> {{ $usuario->name . ' ' . $usuario->last_name }}</b>
                        </li>
                        <!-- Rol del usuario -->
                        <li class="list-group-item text-center">
                            <b><i class="fas fa-user-shield"></i> Rol:</b>
                            <p>
                                @if ($usuario->roles->isNotEmpty())
                                    @foreach ($usuario->roles as $role)
                                        @if ($role->id == 1)
                                            <span class="badge bg-gradient-primary">
                                                <i class="fas fa-crown"></i> {{ ucfirst($role->name) }}
                                            </span>
                                        @elseif ($role->id == 2)
                                            <span class="badge bg-gradient-success">
                                                <i class="fas fa-star"></i> {{ ucfirst($role->name) }}
                                            </span>
                                        @else
                                            <span class="badge bg-gradient-info">
                                                <i class="fas fa-user-tie"></i> {{ ucfirst($role->name) }}
                                            </span>
                                        @endif
                                    @endforeach
                                @else
                                    <span class="badge bg-gradient-secondary">
                                        <i class="fas fa-minus"></i> Sin rol asignado
                                    </span>
                                @endif
                            </p>
                        </li>
                        <!-- Ministerios del usuario -->
                        <li class="list-group-item text-center">
                            <b><i class="fas fa-church"></i> Ministerios:</b>
                            <p>
                                @if ($usuario->ministerios->isNotEmpty())
                                    @foreach ($usuario->ministerios as $ministerio)
                                        <span class="badge badge-info">
                                            <i class="fas fa-users"></i> {{ $ministerio->nombre }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-minus"></i> Sin ministerios asignados
                                    </span>
                                @endif
                            </p>
                        </li>
                        <li class="list-group-item text-center">
                            <b><i class="fas fa-church"></i> Ministerios Liderados:</b>
                            <p>
                                @if ($usuario->ministeriosLiderados->isNotEmpty())
                                    @foreach ($usuario->ministeriosLiderados as $ministerio)
                                        <span class="badge badge-info">
                                            <i class="fas fa-users"></i> {{ $ministerio->nombre }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-minus"></i> Sin ministerios liderados
                                    </span>
                                @endif
                            </p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card card-primary card-outline">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active" href="#general" data-toggle="tab" data-section="general">Información
                                General</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#asistencia" data-toggle="tab" data-section="asistencia">Asistencia</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#agenda" data-toggle="tab" data-section="contraseña">Agenda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#permiso" data-toggle="tab" data-section="contraseña">Permisos</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="general">
                            <div class="row">
                                <!-- Campo Nombre -->
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-user"></i> <strong>Nombre:</strong></label>
                                        <p class="text-muted">{{ $usuario->name ?? 'No disponible' }}</p>
                                    </div>
                                </div>

                                <!-- Campo Apellido -->
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-user-tag"></i> <strong>Apellido:</strong></label>
                                        <p class="text-muted">{{ $usuario->last_name ?? 'No disponible' }}</p>
                                    </div>
                                </div>

                                <!-- Campo Email -->
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-envelope"></i> <strong>Correo:</strong></label>
                                        <p class="text-muted">{{ $usuario->email ?? 'No disponible' }}</p>
                                    </div>
                                </div>

                                <!-- Campo Teléfono -->
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-phone"></i> <strong>Teléfono:</strong></label>
                                        <p class="text-muted">{{ $usuario->phone ?? 'No disponible' }}</p>
                                    </div>
                                </div>

                                <!-- Campo ci -->
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-id-card"></i> <strong>CI:</strong></label>
                                        <p class="text-muted">{{ $usuario->ci ?? 'No disponible' }}</p>
                                    </div>
                                </div>

                                <!-- Campo Dirección -->
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-map-marker-alt"></i>
                                            <strong>Dirección:</strong></label>
                                        <p class="text-muted">{{ $usuario->address ?? 'No disponible' }}</p>
                                    </div>
                                </div>
                            </div>

                            @can('editar configuracion informacion')
                                <a href="{{ route('admin.usuarios.edit', $usuario->id) }}" class="btn btn-success w-100">
                                    <i class="fas fa-edit"></i> Editar información
                                </a>
                            @endcan
                        </div>
                        <div class="tab-pane" id="asistencia">
                            <div class="tab-pane" id="asistencias">
                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="asistencias-table"
                                                    class="table table-striped table-bordered table-hover table-sm datatable text-center">
                                                    <thead>
                                                        <tr>

                                                            <th style="text-align: center">Fecha</th>
                                                            <th style="text-align: center">Hora</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($asitencias as $asistencia)
                                                            <tr>



                                                                <!-- Fecha centrada -->
                                                                <td class="text-center align-middle">
                                                                    @if ($permiso->fecha == null)
                                                                        <small
                                                                            class="badge bg-gradient-warning w-100 h-100">
                                                                            Sin datos
                                                                        </small>
                                                                    @else
                                                                        {{ $permiso->fecha }}
                                                                    @endif
                                                                </td>
                                                                <!-- Fecha centrada -->
                                                                <td class="text-center align-middle">
                                                                    @if ($permiso->hasta == null)
                                                                        <small
                                                                            class="badge bg-gradient-warning w-100 h-100">
                                                                            Sin datos
                                                                        </small>
                                                                    @else
                                                                        {{ $permiso->hasta }}
                                                                    @endif
                                                                </td>
                                                                <!-- Hora de Inicio centrada -->
                                                                <td class="text-center align-middle">
                                                                    @if ($permiso->hora_inicio == null)
                                                                        <small
                                                                            class="badge bg-gradient-warning w-100 h-100">
                                                                            Sin datos
                                                                        </small>
                                                                    @else
                                                                        <small
                                                                            class="badge bg-gradient-primary w-100 h-100">
                                                                            <i class="far fa-clock"></i>
                                                                            {{ $permiso->hora_inicio }}
                                                                        </small>
                                                                    @endif
                                                                </td>

                                                                <!-- Hora Fin centrada -->
                                                                <td class="text-center align-middle">
                                                                    @if ($permiso->hora_fin == null)
                                                                        <small
                                                                            class="badge bg-gradient-warning w-100 h-100">
                                                                            Sin datos
                                                                        </small>
                                                                    @else
                                                                        <small
                                                                            class="badge bg-gradient-primary w-100 h-100">
                                                                            <i class="far fa-clock"></i>
                                                                            {{ $permiso->hora_fin }}
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
                        <div class="tab-pane" id="agenda">
                            <h5 class="text-center">Agenda</h5>

                        </div>
                        <div class="tab-pane" id="permiso">
                            <div class="tab-pane" id="permisos">
                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="permisos-table"
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
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($permisos as $permiso)
                                                            <tr>


                                                                <!-- Usuario Autor centrado -->
                                                                <td class="text-center align-middle">
                                                                    @if ($permiso->usuario == null)
                                                                        <small
                                                                            class="badge bg-gradient-warning w-100 h-100">
                                                                            Sin datos
                                                                        </small>
                                                                    @else
                                                                        {{ $permiso->usuario->name }}
                                                                    @endif
                                                                </td>

                                                                <!-- Fecha centrada -->
                                                                <td class="text-center align-middle">
                                                                    @if ($permiso->fecha == null)
                                                                        <small
                                                                            class="badge bg-gradient-warning w-100 h-100">
                                                                            Sin datos
                                                                        </small>
                                                                    @else
                                                                        {{ $permiso->fecha }}
                                                                    @endif
                                                                </td>
                                                                <!-- Fecha centrada -->
                                                                <td class="text-center align-middle">
                                                                    @if ($permiso->hasta == null)
                                                                        <small
                                                                            class="badge bg-gradient-warning w-100 h-100">
                                                                            Sin datos
                                                                        </small>
                                                                    @else
                                                                        {{ $permiso->hasta }}
                                                                    @endif
                                                                </td>
                                                                <!-- Hora de Inicio centrada -->
                                                                <td class="text-center align-middle">
                                                                    @if ($permiso->hora_inicio == null)
                                                                        <small
                                                                            class="badge bg-gradient-warning w-100 h-100">
                                                                            Sin datos
                                                                        </small>
                                                                    @else
                                                                        <small
                                                                            class="badge bg-gradient-primary w-100 h-100">
                                                                            <i class="far fa-clock"></i>
                                                                            {{ $permiso->hora_inicio }}
                                                                        </small>
                                                                    @endif
                                                                </td>

                                                                <!-- Hora Fin centrada -->
                                                                <td class="text-center align-middle">
                                                                    @if ($permiso->hora_fin == null)
                                                                        <small
                                                                            class="badge bg-gradient-warning w-100 h-100">
                                                                            Sin datos
                                                                        </small>
                                                                    @else
                                                                        <small
                                                                            class="badge bg-gradient-primary w-100 h-100">
                                                                            <i class="far fa-clock"></i>
                                                                            {{ $permiso->hora_fin }}
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
    </div>
@stop

@push('breadcrumb-plugins')
    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary rounded">
        <i class="fas fa-undo"></i> Regresar
    </a>
@endpush

@push('css')
@endpush
