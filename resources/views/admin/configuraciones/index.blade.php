@extends('adminlte::page')

@section('title', $pageTitle)

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle"
                            src="{{ asset($configuracion->logo ?? 'images/default-dark.png') }}"
                            alt="Logotipo del sistema">
                    </div>

                                    
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item text-center">
                            <b>{{ $configuracion->nombre }}</b>
                        </li>
                        <li class="list-group-item text-center">
                            <p class="text-muted ">{{ $configuracion->descripcion }}</p>
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
                            <a class="nav-link active" href="#general" data-toggle="tab">Configuración General</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#logotipos" data-toggle="tab">Logotipo y Favicon</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Configuración General -->
                        <div class="active tab-pane" id="general">
                            @include('admin.configuraciones.form', ['configuracion' => $configuracion])
                        </div>
                        <!-- Logotipo y Favicon -->
                        <div class="tab-pane" id="logotipos">
                            @include('admin.configuraciones.logotipos', ['configuracion' => $configuracion])
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
