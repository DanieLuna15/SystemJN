@extends('layouts.errors')

@section('title', 'Puerta de Enlace Incorrecta')

@section('body-class', 'error-502')

@section('content')
    <div class="error-content">
        <h1 class="text-danger">502</h1>
        <h3 class="error-message">¡Lo siento! El servidor recibió una respuesta inválida de otro servidor.</h3>
        <a href="{{ route('home') }}" class="btn bg-gradient-info">Regresar a Inicio</a>
    </div>
@endsection

@section('script')
    <!-- Puedes agregar scripts aquí si es necesario -->
@endsection
