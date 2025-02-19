@extends('adminlte::page')

@section('title', $pageTitle)

@section('content')
    @include('admin.ministerios.form', ['ministerio' => new \App\Models\Ministerio()])
@stop
