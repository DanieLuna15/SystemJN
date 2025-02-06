@extends('adminlte::page')

@section('title', 'Ministerios')

@section('content_header')
    <h1>Gestión de Ministerios</h1>
@stop

{{-- Push extra CSS --}}

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{-- {{ route('admin.ministerios.create') }} --}}" class="btn btn-primary">Agregar Ministerio</a>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Logo</th>
                        <th>Nombre</th>
                        <th>Multa (Bs)</th>
                        <th>Hora de Tolerancia</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ministerios as $ministerio)
                        <tr>
                            <td>
                                @if ($ministerio->logo)
                                    <img src="{{ asset($ministerio->logo) }}" alt="Logo" width="50">
                                @else
                                    <span class="text-muted font-italic">Sin imagen</span>
                                @endif
                            </td>
                            <td>{{ $ministerio->nombre }}</td>
                            <td>{{ $ministerio->multa_incremento }} Bs.</td>
                            <td>{{ $ministerio->hora_tolerancia }}</td>
                            <td>
                                <a href="{{-- {{ route('ministerios.edit', $ministerio) }} --}}" class="btn btn-warning btn-sm">Editar</a>
                                <form action="{{-- {{ route('ministerios.destroy', $ministerio) }} --}}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('¿Seguro que deseas eliminar?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>
@stop

@push('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@endpush

{{-- Push extra scripts --}}

@push('js')
    <script>
        console.log("Hi, I'm using the Laravel-AdminLTE package!");
    </script>
@endpush
