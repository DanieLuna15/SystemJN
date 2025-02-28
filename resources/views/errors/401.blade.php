@extends('layouts.errors')

@section('title', 'No Autorizado')

@section('body-class', 'error-401')

@section('content')
    <div class="error-content">
        <h1 class="text-danger">401</h1>
        <h3 class="error-message">¡Lo siento! No tienes autorización para acceder a esta página.</h3>
        <a href="{{ route('home') }}" class="btn bg-gradient-info">Regresar a Inicio</a>
    </div>
@endsection

@section('script')
    <!-- Puedes agregar scripts aquí si es necesario -->
@endsection
