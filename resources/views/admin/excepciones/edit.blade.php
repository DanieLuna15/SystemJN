@extends('adminlte::page')

@section('title', $pageTitle)

@section('content')
    @include('admin.excepciones.form', ['excepcion' => $excepcion])
@stop

@push('js')

@endpush