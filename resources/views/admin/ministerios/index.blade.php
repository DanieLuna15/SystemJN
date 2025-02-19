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
                            class="table table-striped table-bordered table-hover table-sm datatable text-center">
                            <thead class="text-center">
                                <tr>
                                    <th>Imagen</th>
                                    <th>Nombre</th>
                                    <th>Monto Sancion (Bs)</th>
                                    <th>Estado</th>
                                    <th>Categoria</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ministerios as $ministerio)
                                    <tr>
                                        <!-- Imagen centrada -->
                                        <td class="align-middle">
                                            @if ($ministerio->logo)
                                                <img src="{{ asset($ministerio->logo) }}"
                                                    title="Imagen referencial" class="img-rounded">
                                            @else
                                                <img src="{{ asset('images/default-dark.png') }}" title="Sin imagen"
                                                    class="img-rounded">
                                            @endif
                                        </td>

                                        <td class="align-middle text-truncate" style="max-width: 100px;">
                                            {{ __(strLimit($ministerio->nombre, 30)) }}
                                        </td>

                                        <!-- Monto sanción centrado -->
                                        <td class="align-middle">{{ $ministerio->multa_incremento }} Bs.</td>

                                        <!-- Estado centrado -->
                                        <td class="align-middle">
                                            <div class="d-flex justify-content-center">
                                                {!! $ministerio->statusBadge !!}
                                            </div>
                                        </td>

                                        <!-- Categoria centrada -->
                                        <td class="align-middle">
                                            @if ($ministerio->tipo == 1)
                                                <small class="badge bg-gradient-primary w-100 h-100"><i
                                                        class="fas fa-crown"></i> Alto</small>
                                            @else
                                                <small class="badge bg-gradient-info w-100 h-100"><i
                                                        class="fas fa-star"></i> Estándar</small>
                                            @endif
                                        </td>

                                        <!-- Acciones centradas -->
                                        <td class="align-middle">
                                            <div class="d-flex justify-content-center">
                                                <a href="{{ route('admin.ministerios.edit', $ministerio) }}"
                                                    class="btn btn-warning btn-sm mx-1" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" title="Cambiar estado"
                                                    class="btn btn-sm {{ $ministerio->estado ? 'btn-danger' : 'btn-success' }} confirmationBtn mx-1"
                                                    data-action="{{ route('admin.ministerios.status', $ministerio->id) }}"
                                                    data-question="{{ $ministerio->estado ? '¿Seguro que deseas inhabilitar el Ministerio <strong>' . $ministerio->nombre . '</strong>?' : '¿Seguro que deseas habilitar el Ministerio <strong>' . $ministerio->nombre . '</strong>?' }}">
                                                    <i
                                                        class="fas {{ $ministerio->estado ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                                </button>

                                                <a href="{{ route('admin.ministerios.horarios', $ministerio) }}"
                                                    class="btn btn-secondary btn-sm mx-1" title="Verificar Horarios">
                                                    <i class="fas fa-list-ul" style="color: #63E6BE;"></i>
                                                </a>
                                            </div>
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
