@extends('adminlte::page')

@section('title', $pageTitle)

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle" 
                         src="{{ asset('storage/' . $configuracion->logo ?? 'images/default-dark.png') }}" 
                         alt="Logotipo del sistema">
                </div>
                <h3 class="profile-username text-center">{{ $configuracion->nombre }}</h3>
                <p class="text-muted text-center">{{ $configuracion->descripcion }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#general" data-toggle="tab">Configuración General</a></li>
                    <li class="nav-item"><a class="nav-link" href="#logotipos" data-toggle="tab">Logotipo y Favicon</a></li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Configuración General -->
                    <div class="active tab-pane" id="general">
                        <dl class="row">
                            <dt class="col-sm-4">Dirección</dt>
                            <dd class="col-sm-8">{{ $configuracion->direccion }}</dd>

                            <dt class="col-sm-4">Teléfono</dt>
                            <dd class="col-sm-8">{{ $configuracion->telefono }}</dd>

                            <dt class="col-sm-4">Email</dt>
                            <dd class="col-sm-8">{{ $configuracion->email }}</dd>

                            <dt class="col-sm-4">URL del sistema</dt>
                            <dd class="col-sm-8"><a href="{{ $configuracion->url }}" target="_blank">{{ $configuracion->url }}</a></dd>
                        </dl>
                    </div>
                    
                    <!-- Logotipo y Favicon -->
                    <div class="tab-pane" id="logotipos">
                        <div class="text-center">
                            <img class="img-thumbnail" src="{{ asset('storage/' . $configuracion->logo ?? 'images/default-dark.png') }}" 
                                 alt="Logotipo del sistema" width="200px">
                            <p class="mt-2">Logotipo actual</p>
                            
                            <img class="img-thumbnail" src="{{ asset('storage/' . $configuracion->favicon ?? 'images/default-favicon.png') }}" 
                                 alt="Favicon del sistema" width="100px">
                            <p class="mt-2">Favicon actual</p>
                        </div>
                    </div>
                </div>
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
