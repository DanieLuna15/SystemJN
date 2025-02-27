@extends('layouts.errors')

@section('title', 'Error Interno del Servidor')

@section('body-class', 'error-500')

@section('css')
<!-- Incluye el archivo CSS separado -->
<link rel="stylesheet" href="{{ asset('css/customError.css') }}">
@endsection

@section('content')
<div class="error-content">
    <h1 class="text-danger">500</h1>
    <h3 class="error-message">¡Lo siento! Ha ocurrido un error interno en el servidor.</h3>
    <a href="{{ route('home') }}" class="btn btn-primary">Regresar al Inicio</a>
</div>
@endsection

@section('script')
<!-- Puedes agregar scripts aquí si es necesario -->
@endsection
