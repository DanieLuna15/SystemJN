@extends('adminlte::page')
@section('title', $pageTitle)

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile text-center">
                    <!-- Imagen de perfil -->
                    <img class="profile-user-img img-fluid img-circle"
                        src="{{ asset($usuario->profile_image ?? 'images/default-dark.png') }}" alt="Foto de perfil">

                    <!-- datos del usuario -->
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item text-center">
                            <b>{{ $usuario->name . ' ' . $usuario->last_name }}</b>
                        </li>
                        <li class="list-group-item text-center">
                            <p class="text-muted ">{{ $usuario->address }}</p>
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
                            <a class="nav-link" href="#agenda" data-toggle="tab"
                                data-section="contraseña">Agenda</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="general">
                            <h5 class="text-center">Informacion General</h5>
                            <!-- Aquí puedes agregar más información -->

                            <form action="{{ route('admin.usuarios.update', $usuario->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT') <!-- Esto asegura que se use el método PUT -->
                                <input type="hidden" name="form_type" value="secundario">
                                <div class="row">
                            
                                    <div class="col-md-6 col-lg-6">
                                        <!-- Campo Nombre -->
                                        <x-adminlte-input name="name" label="Nombre:" value="{{ old('name', $usuario->name ?? '') }}" />
                                    </div>
                                    <div class="col-md-6 col-lg-6">
                                        <!-- Campo Apellido -->
                                        <x-adminlte-input name="last_name" label="Apellido:" value="{{ old('last_name', $usuario->last_name ?? '') }}" />
                                    </div>
                            
                                    <div class="col-md-6 col-lg-6">
                                        <!-- Campo Email -->
                                        <x-adminlte-input name="email" label="Correo:" value="{{ old('email', $usuario->email ?? '') }}" />
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
                                        <x-adminlte-button class="btn w-100" type="submit" label="Guardar cambios" theme="success"
                                            icon="fas fa-lg fa-save" />
                                    </div>
                                @endcan
                            </form>
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
