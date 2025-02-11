@extends('adminlte::page')

{{-- Customize layout sections --}}

@section('content_header')
    <h1><b>Configuracion del Sistema</b></h1>
    <hr>
@stop
{{-- Content body: main page content --}}

@section('content')
    <div class="row">

    </div>
@stop

{{-- Push extra CSS --}}

@push('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@endpush

{{-- Push extra scripts --}}

@push('js')
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@endpush
