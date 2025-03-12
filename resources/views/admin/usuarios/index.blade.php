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
                                <th class="no-export">Imagen</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>CI</th>
                                <th>Ministerio</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Correo</th>
                                <th class="no-export">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($usuarios as $usuario)
                                <tr>
                                    <!-- Imagen centrada -->
                                    <td class="align-middle">
                                        @if ($usuario->profile_image)
                                            <img src="{{ asset($usuario->profile_image) }}" title="Imagen referencial"
                                                class="img-rounded">
                                        @else
                                            <img src="{{ asset('images/default-dark.png') }}" title="Sin imagen"
                                                class="img-rounded">
                                        @endif
                                    </td>
                                    <!-- Nombre centrado -->
                                    <td class="align-middle text-truncate" style="max-width: 100px;">
                                        {{ __(strLimit($usuario->name, 30)) }}
                                    </td>

                                    <!-- Apellido centrado -->
                                    <td class="align-middle text-truncate" style="max-width: 100px;">
                                        {{ __(strLimit($usuario->last_name, 30)) }}
                                    </td>

                                    <!-- CI centrado -->
                                    <td class="align-middle">{{ $usuario->ci }}</td>

                                    <!-- Ministerio centrado -->
                                    <td class="align-middle text-truncate">
                                        @if($usuario->ministerios->isNotEmpty())
                                            @foreach ($usuario->ministerios as $ministerio)
                                                <span class="badge badge-info">{{ $ministerio->name }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">Sin ministerios asignados</span>
                                        @endif
                                    </td>


                                    <!-- Categoria centrada -->
                                    <td class="align-middle">
                                        @if ($usuario->tipo == 1)
                                            <small class="badge bg-gradient-primary w-100 h-100"><i class="fas fa-crown"></i>
                                                Alto</small>
                                        @else
                                            <small class="badge bg-gradient-info w-100 h-100"><i class="fas fa-star"></i>
                                                Estándar</small>
                                        @endif
                                    </td>

                                    <!-- Estado centrado -->
                                    <td class="align-middle">
                                        <div class="d-flex justify-content-center">
                                            {!! $usuario->statusBadge !!}
                                        </div>
                                    </td>

                                    <!-- Nombre correo -->
                                    <td class="align-middle text-truncate" style="max-width: 100px;">
                                        {{ __(strLimit($usuario->email, 30)) }}
                                    </td>

                                    <!-- Acciones centradas -->
                                    <td class="align-middle">
                                        <div class="d-flex justify-content-center">
                                            @can('editar ministerios')
                                                <a href="{{ route('admin.usuarios.edit', $usuario) }}"
                                                    class="btn btn-warning btn-sm mx-1" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan

                                            @can('cambiar estado ministerios')
                                                <button type="button" title="Cambiar estado"
                                                    class="btn btn-sm {{ $usuario->estado ? 'btn-danger' : 'btn-success' }} confirmationBtn mx-1"
                                                    data-action="{{ route('admin.usuarios.status', $usuario->id) }}"
                                                    data-question="{{ $usuario->estado ? '¿Seguro que deseas inhabilitar el Usuario <strong>' . $usuario->name . '</strong>?' : '¿Seguro que deseas habilitar el Usuario <strong>' . $usuario->name . '</strong>?' }}">
                                                    <i class="fas {{ $usuario->estado ? 'fa-eye-slash' : 'fa-eye' }}">
                                                    </i>
                                                </button>
                                            @endcan

                                            @can('ver horarios_ministerio')
                                                <a href="{{ route('admin.ministerios.horarios', $usuario) }}"
                                                    class="btn btn-secondary btn-sm mx-1" title="Verificar Horarios">
                                                    <i class="fas fa-list-ul" style="color: #63E6BE;"></i>
                                                </a>
                                            @endcan
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
    @can('crear usuarios')
        <a href="{{ route('admin.ministerios.create') }}" class="btn btn-success rounded">
            <i class="fas fa-plus-square"></i> Nuevo
        </a>
    @endcan
@endpush