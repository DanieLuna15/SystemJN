@extends('adminlte::page')

@section('title', $pageTitle)

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
                        <table id="ministerios-table"
                            class="table table-striped table-bordered table-hover table-sm datatable">
                            <thead>
                                <tr>
                                    <th style="text-align: center">Imagen</th>
                                    <th style="text-align: center">Nombre</th>
                                    <th style="text-align: center">Monto multa (Bs)</th>
                                    <th style="text-align: center">Estado</th>
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
                                        <td class="text-center">
                                            <a href="{{ route('admin.ministerios.edit', $ministerio) }}"
                                                class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
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
    <script></script>
@endpush
