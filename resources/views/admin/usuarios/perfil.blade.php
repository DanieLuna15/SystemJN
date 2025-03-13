@extends('adminlte::page')
@section('title', 'Perfil de Usuario')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile text-center">
                    <!-- Imagen de perfil -->
                    <img class="profile-user-img img-fluid img-circle"
                        src="{{ asset($configuracion->logo ?? 'images/default-dark.png') }}"
                        alt="Foto de perfil">

                    <!-- Nombre del usuario -->
                    <h3 class="profile-username mt-2">{{ Auth::user()->name }}</h3>

                    <!-- Descripción del usuario -->
                    <form action="{{ route('profile.updateDescription') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="3" required>{{ Auth::user()->descripcion }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">Actualizar Información</button>
                    </form>
                </div>
            </div>

            <!-- Formulario de cambio de contraseña -->
            <div class="card card-primary card-outline">
                <div class="card-body">
                    <h5 class="text-center">Cambiar Contraseña</h5>
                    <form action="{{ route('profile.updatePassword') }}" method="POST">
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
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@push('css')
    <style>
        .profile-user-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #ddd;
        }
    </style>
@endpush

