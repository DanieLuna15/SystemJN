@extends('layouts.errors')

@section('title', 'No Encontrado')

@section('body-class', 'error-404')

@section('content')
    <div class="error-content">
        <h1 class="text-danger">404</h1>
        <h3 class="error-message">¡Lo siento! No se encontró la página que estás buscando.</h3>
        <a href="{{ route('home') }}" class="btn bg-gradient-info">Regresar a Inicio</a>
    </div>
@endsection

@section('script')
    <!-- Puedes agregar scripts aquí si es necesario -->
@endsection
