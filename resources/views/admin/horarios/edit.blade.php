@extends('adminlte::page')

@section('title', 'Editar Horario')

@section('content_header')
    <h1>Editar Horario</h1>
@stop

@section('content')
    @include('admin.horarios.form', ['horario' => $horario])
@stop