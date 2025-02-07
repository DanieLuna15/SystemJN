@extends('adminlte::page')

@section('title', 'Editar Ministerio')

@section('content_header')
    <h1>Editar Ministerio</h1>
@stop

@section('content')
    @include('admin.ministerios.form', ['ministerio' => $ministerio])
@stop
