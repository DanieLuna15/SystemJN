@extends('adminlte::page')

@section('title', 'Ministerios')

@section('content_header')
    <h1>Gestión de Ministerios</h1>
@stop

{{-- Push extra CSS --}}

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <div class="card-title">Listado de Ministerios</div>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>

                        <a href="{{ route('admin.ministerios.create') }}" class="btn btn-success"> + Agregar
                            Ministerio</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="ministerios-table" class="table table-striped table-bordered table-hover table-sm">
                            <thead>
                                <tr>
                                    <th style="text-align: center">Logo</th>
                                    <th style="text-align: center">Nombre</th>
                                    <th style="text-align: center">Multa (Bs)</th>
                                    <th style="text-align: center">Hora de Tolerancia</th>
                                    <th style="text-align: center">Acciones</th>
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
                                        <td class="text-center">
                                            <a href="{{ route('admin.ministerios.edit', $ministerio) }}"
                                                class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('admin.ministerios.destroy', $ministerio) }}"
                                                method="POST" style="display:inline-block;" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>


@stop

@push('css')
@endpush

{{-- Push extra scripts --}}

@push('js')
    <script>
        $('#ministerios-table').DataTable([

        ]);
    </script>
@endpush
