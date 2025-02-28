@extends('layouts.errors')

@section('title', 'Método No Permitido')

@section('body-class', 'error-405')

@section('content')
    <div class="error-content">
        <h1 class="text-danger">405</h1>
        <h3 class="error-message">¡Lo siento! El método solicitado no está permitido.</h3>
        <a href="{{ route('home') }}" class="btn bg-gradient-info">Regresar a Inicio</a>
    </div>
@endsection

@section('script')
    <!-- Puedes agregar scripts aquí si es necesario -->
@endsection
