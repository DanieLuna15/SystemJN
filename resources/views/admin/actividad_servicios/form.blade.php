<x-adminlte-card>
    <x-slot name="header">
        <h3 class="card-title">{{ isset($actividad_servicio->id) ? 'Actualizar Ministerio' : 'Nuevo Ministerio' }}</h3>
    </x-slot>

    <div class="card-body">
        <form action="{{ route('admin.actividad_servicios.save', $actividad_servicio->id ?? null) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{ $actividad_servicio->id ?? '' }}">
            <div class="row">
                <div class="col-lg-4">
                    <div class="col-sm-12">
                        <!-- Campo Imagen -->
                        <x-image-upload label="Imagen" name="imagen" :image="$actividad_servicio->imagen ?? null" :id="'actividad_servicio'" />
                    </div>

                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <!-- Campo Nombre -->
                            <x-adminlte-input name="nombre" label="Nombre:"
                                value="{{ old('nombre', $actividad_servicio->nombre ?? '') }}" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="d-flex justify-content-between">
                <x-adminlte-button class="btn" type="submit"
                    label="{{ isset($actividad_servicio->id) ? 'Actualizar' : 'Guardar' }}" theme="success"
                    icon="fas fa-lg fa-save" />
                <a href="{{ route('admin.actividad_servicios.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</x-adminlte-card>
@push('breadcrumb-plugins')
    <a href="{{ route('admin.actividad_servicios.index') }}" class="btn btn-secondary rounded">
        <i class="fas fa-undo"></i> Regresar
    </a>
@endpush