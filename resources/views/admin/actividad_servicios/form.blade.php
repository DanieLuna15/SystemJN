<x-adminlte-card>
    <x-slot name="header">
        <h3 class="card-title">{{ isset($actividad_servicio->id) ? 'Actualizar Ministerio' : 'Nuevo Ministerio' }}</h3>
    </x-slot>

    <div class="card-body">
        <form action="{{ route('admin.actividad_servicios.save', $actividad_servicio->id ?? null) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{ $actividad_servicio->id ?? '' }}">

            <!-- Campo Nombre -->
            <x-adminlte-input name="nombre" label="Nombre:"
                value="{{ old('nombre', $actividad_servicio->nombre ?? '') }}" />

            <!-- Campo Imagen -->
            <x-image-upload label="Imagen" name="imagen" :image="$actividad_servicio->imagen ?? null" :id="'actividad_servicio'" />

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
