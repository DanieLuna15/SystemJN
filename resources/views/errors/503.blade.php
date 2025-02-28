@extends('layouts.errors')

@section('title', 'Servicio No Disponible')

@section('body-class', 'error-503')

@section('content')
    <div class="error-content">
        <h1 class="text-danger">503</h1>
        <h3 class="error-message">¡Lo siento! El servicio no está disponible en este momento.</h3>
        <a href="{{ route('home') }}" class="btn bg-gradient-info">Regresar a Inicio</a>
    </div>
@endsection

@section('script')
    <!-- Puedes agregar scripts aquí si es necesario -->
@endsection
