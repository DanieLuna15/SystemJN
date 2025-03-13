@extends('adminlte::page')
@section('title', $pageTitle)

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile text-center">
                    <!-- Imagen de perfil -->
                    <img class="profile-user-img img-fluid img-circle"
                        src="{{ asset($user->profile_image ?? 'images/default-dark.png') }}" alt="Foto de perfil">

                    <!-- datos del usuario -->
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item text-center">
                            <b>{{ $user->name }}</b>
                        </li>
                        <li class="list-group-item text-center">
                            <p class="text-muted ">{{ $user->address }}</p>
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
                            <a class="nav-link" href="#foto_perfil" data-toggle="tab" data-section="foto_perfil">Foto de
                                Perfil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contraseña" data-toggle="tab"
                                data-section="contraseña">Contraseña</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="general">
                            <h5 class="text-center">Informacion General</h5>
                            <!-- Aquí puedes agregar más información -->
                        </div>
                        <div class="tab-pane" id="foto_perfil">
                            <h5 class="text-center">foto de perfil</h5>
                            <!-- Aquí puedes agregar el formulario para cambiar foto -->
                        </div>
                        <div class="tab-pane" id="contraseña">
                            <h5 class="text-center">Cambiar Contraseña</h5>
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

@push('css')
@endpush
