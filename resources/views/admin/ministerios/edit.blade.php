@extends('adminlte::page')

@section('title', $pageTitle)

@section('content')
    @include('admin.ministerios.form', ['ministerio' => $ministerio])
@stop
