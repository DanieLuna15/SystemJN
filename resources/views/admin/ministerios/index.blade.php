@extends('adminlte::page')

@section('title', $pageTitle)

{{-- Push extra CSS --}}

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="ministerios-table"
                            class="table table-striped table-bordered table-hover table-sm datatable">
                            <thead>
                                <tr>
                                    <th style="text-align: center">Imagen</th>
                                    <th style="text-align: center">Nombre</th>
                                    <th style="text-align: center">Monto multa (Bs)</th>
                                    <th style="text-align: center">Estado</th>
                                    <th style="text-align: center">Tipo</th>
                                    <th style="text-align: center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ministerios as $ministerio)
                                    <tr>
                                        <td class="text-center">
                                            @if ($ministerio->logo)
                                                <img src="{{ asset($ministerio->logo) }}" alt="Logo" width="50">
                                            @else
                                                <span class="text-muted font-italic">Sin imagen</span>
                                            @endif
                                        </td>
                                        <td>{{ $ministerio->nombre }}</td>
                                        <td class="text-center">{{ $ministerio->multa_incremento }} Bs.</td>

                                        <td class="text-center align-middle">
                                            <div class="d-flex justify-content-center">
                                                {!! $ministerio->statusBadge !!}
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            @if ($ministerio->tipo == 1)
                                                <small class="badge bg-gradient-primary w-100 h-100"><i
                                                        class="fas fa-solid fa-crown"></i> Alto</small>
                                            @else
                                                <small class="badge bg-gradient-info w-100 h-100"><i
                                                        class="fas fa-star"></i> Estandar</small>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            <a href="{{ route('admin.ministerios.edit', $ministerio) }}"
                                                class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" title="Cambiar estado"
                                                class="btn btn-sm {{ $ministerio->estado ? 'btn-danger' : 'btn-success' }} confirmationBtn"
                                                data-action="{{ route('admin.ministerios.status', $ministerio->id) }}"
                                                data-question="{{ $ministerio->estado ? '¿Seguro que deseas inhabilitar el Ministerio <strong>' . $ministerio->nombre . '</strong>?' : '¿Seguro que deseas habilitar el Ministerio <strong>' . $ministerio->nombre . '</strong>?' }}">
                                                <i class="fas {{ $ministerio->estado ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                            </button>

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
@endpush

@push('breadcrumb-plugins')
    <a href="{{ route('admin.ministerios.create') }}" class="btn btn-success rounded">
        <i class="fas fa-plus-square"></i> Nuevo
    </a>
@endpush

