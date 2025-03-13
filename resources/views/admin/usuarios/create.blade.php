@extends('adminlte::page')

@section('title', $pageTitle)

@section('content')
    @include('admin.usuarios.form', ['usuario' => new \App\Models\User()])
@stop
