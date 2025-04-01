@extends('adminlte::page')

@section('title', $pageTitle)

@section('content')
    @include('admin.permisos.form', ['permiso' => new \App\Models\Permiso()])
@stop

@push('js')

@endpush
