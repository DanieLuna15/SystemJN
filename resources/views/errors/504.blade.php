@extends('layouts.errors')

@section('title', 'Tiempo de Espera de la Puerta de Enlace')

@section('body-class', 'error-504')

@section('css')
<!-- Incluye el archivo CSS separado -->
<link rel="stylesheet" href="{{ asset('css/customError.css') }}">
@endsection

@section('content')
<div class="error-content">
    <h1 class="text-danger">504</h1>
    <h3 class="error-message">¡Lo siento! El servidor actuando como puerta de enlace no recibió una respuesta a tiempo.</h3>
    <a href="{{ route('home') }}" class="btn btn-primary">Regresar al Inicio</a>
</div>
@endsection

@section('script')
<!-- Puedes agregar scripts aquí si es necesario -->
@endsection
