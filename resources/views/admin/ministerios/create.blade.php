@extends('adminlte::page')

@section('title', 'Nuevo Ministerio')

@section('content_header')
    <h1>Nuevo Ministerio</h1>
@stop

@section('content')
    @include('admin.ministerios.form', ['ministerio' => new \App\Models\Ministerio()])
@stop