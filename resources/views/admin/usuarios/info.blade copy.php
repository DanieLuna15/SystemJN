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
                            <a class="nav-link" href="#asistencia" data-toggle="tab" data-section="asistencia">Asistencia
                                y Multas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#agenda" data-toggle="tab" data-section="contraseña">Agenda</a>
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

                                <!-- Campo Teléfono -->
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



                        {{-- <form action="{{ route('admin.usuarios.update', $usuario->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT') <!-- Esto asegura que se use el método PUT -->
                            <input type="hidden" name="form_type" value="secundario">
                            <div class="row">

                                <div class="col-md-6 col-lg-6">
                                    <!-- Campo Nombre -->
                                    <x-adminlte-input name="name" label="Nombre:"
                                        value="{{ old('name', $usuario->name ?? '') }}" />
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <!-- Campo Apellido -->
                                    <x-adminlte-input name="last_name" label="Apellido:"
                                        value="{{ old('last_name', $usuario->last_name ?? '') }}" />
                                </div>

                                <div class="col-md-6 col-lg-6">
                                    <!-- Campo Email -->
                                    <x-adminlte-input name="email" label="Correo:"
                                        value="{{ old('email', $usuario->email ?? '') }}" />
                                </div>

                                <div class="col-md-6 col-lg-6">
                                    <!-- Campo Telefono -->
                                    <x-adminlte-input name="phone" label="Telefono:"
                                        value="{{ old('phone', $usuario->phone ?? '') }}" />
                                </div>

                                <div class="col-md-12 col-lg-12">
                                    <!-- Campo Direccion -->
                                    <x-adminlte-input name="address" label="Dirección:"
                                        value="{{ old('address', $usuario->address ?? '') }}" />
                                </div>

                            </div>

                            @can('editar configuracion informacion')
                            <div class="d-flex justify-content-between">
                                <x-adminlte-button class="btn w-100" type="submit" label="Guardar cambios"
                                    theme="success" icon="fas fa-lg fa-save" />
                            </div>
                            @endcan
                        </form> --}}
                    </div>
                    <div class="tab-pane" id="asistencia">
                        <h5 class="text-center">Asistencia y Multas</h5>
                        <!-- Aquí puedes agregar el formulario para cambiar foto -->
                    </div>
                    <div class="tab-pane" id="agenda">
                        <h5 class="text-center">Agenda</h5>
                        {{-- <form action="{{ route('profile.updatePassword') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="password_actual">Contraseña Actual</label>
                                <input type="password" name="password_actual" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="password_nueva">Nueva Contraseña</label>
                                <input type="password" name="password_nueva" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="password_confirm">Confirmar Nueva Contraseña</label>
                                <input type="password" name="password_confirm" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Actualizar Contraseña</button>
                        </form> --}}
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
