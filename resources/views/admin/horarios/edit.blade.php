@extends('adminlte::page')

@section('title', $pageTitle)

@section('content')
    @include('admin.horarios.form', ['horario' => $horario])
@stop

@push('js')

@endpush