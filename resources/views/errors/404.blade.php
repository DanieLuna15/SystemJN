@extends('layouts.errors')

@section('title', 'Acceso Denegado')

@section('content')
<div class="text-center mt-5">
    <h1 class="text-danger">404</h1>
    <h3>No se encontro la pagina.</h3>
    <a href="{{ route('home') }}" class="btn btn-primary">Regresar al Inicio</a>
</div>
@endsection
@push('css')
@endpush

@push('script')
@endpush