@extends('adminlte::page')

@section('title', $pageTitle)

@section('content')
    @include('admin.horarios.form', ['horario' => new \App\Models\Horario()])
@stop
