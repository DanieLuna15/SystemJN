@extends('adminlte::page')

@section('title', $pageTitle)

@section('content')
    @include('admin.reglas_multas.form', ['regla_multa' => new \App\Models\ReglaMulta()])
@stop

@push('js')

@endpush
