@extends('adminlte::page')

@section('title', $pageTitle)

@section('content')
    @include('admin.permisos.form', ['permiso' => $permiso])
@stop

@push('js')

@endpush