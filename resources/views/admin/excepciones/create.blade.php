@extends('adminlte::page')

@section('title', $pageTitle)

@section('content')
    @include('admin.excepciones.form', ['excepcion' => new \App\Models\Excepcion()])
@stop

@push('js')

@endpush
