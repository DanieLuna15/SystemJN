@extends('adminlte::page')

@section('title', $pageTitle)

@section('content')
    @include('admin.usuarios.form', ['usuario' => $usuario])
@stop
