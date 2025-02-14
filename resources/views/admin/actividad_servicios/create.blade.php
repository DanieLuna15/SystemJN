@extends('adminlte::page')

@section('title', $pageTitle)

@section('content')
    @include('admin.actividad_servicios.form', ['actividad_servicio' => new \App\Models\ActividadServicio()])
@stop