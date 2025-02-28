@extends('layouts.errors')

@section('title', 'Error Interno del Servidor')

@section('body-class', 'error-500')

@section('content')
    <div class="error-content">
        <h1 class="text-danger">500</h1>
        <h3 class="error-message">¡Lo siento! Ha ocurrido un error interno en el servidor.</h3>
        <a href="{{ route('home') }}" class="btn bg-gradient-info">Regresar a Inicio</a>
    </div>
@endsection

@section('script')
    <!-- Puedes agregar scripts aquí si es necesario -->
@endsection
