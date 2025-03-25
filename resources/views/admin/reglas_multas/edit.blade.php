@extends('adminlte::page')

@section('title', $pageTitle)

@section('content')
    @include('admin.reglas_multas.form', ['regla_multa' => $regla_multa])
@stop

@push('js')

@endpush