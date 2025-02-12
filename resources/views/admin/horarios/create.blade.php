@extends('adminlte::page')

@section('title', 'Nuevo Horario')

@section('content_header')
    <h1>Nuevo Horario</h1>
@stop

@section('content')
    @include('admin.horarios.form', ['horario' => new \App\Models\Horario()])
@stop