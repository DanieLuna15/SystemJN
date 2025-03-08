@extends('adminlte::page')

@section('title', $pageTitle)

{{-- Push extra CSS --}}

@section('content')

@stop
@push('css')
@endpush

{{-- Push extra scripts --}}

@push('js')
@endpush

@push('breadcrumb-plugins')
    {{-- @can('crear ministerios') --}}
    <a href="{{ route('admin.reportes.exportar') }}" class="btn btn-info rounded">
        <i class="fas fa-file-export"></i> Exportar Reporte
    </a>
    {{-- @endcan --}}
@endpush
