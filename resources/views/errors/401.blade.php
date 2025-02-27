@extends('layouts.errors')

@section('title', 'No Autorizado')

@section('body-class', 'error-401')

@section('css')
<!-- Incluye el archivo CSS separado -->
<link rel="stylesheet" href="{{ asset('css/customError.css') }}">
@endsection

@section('content')
<div class="error-content">
    <h1 class="text-danger">401</h1>
    <h3 class="error-message">¡Lo siento! No tienes autorización para acceder a esta página.</h3>
    <a href="{{ route('home') }}" class="btn btn-primary">Regresar al Inicio</a>
</div>
@endsection

@section('script')
<!-- Puedes agregar scripts aquí si es necesario -->
@endsection
