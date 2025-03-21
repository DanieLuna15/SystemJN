@php
    $select2Config = config('select2');
@endphp

<x-adminlte-card>
    <form action="{{ route('admin.ministerios.save', $ministerio->id ?? null) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{ $ministerio->id ?? '' }}">
        <div class="row">
            <div class="col-lg-4">
                <div class="col-sm-12">
                    <!-- Campo Imagen -->
                    <x-image-upload label="Imagen" name="logo" :image="$ministerio->logo ?? null"
                        :id="'logotipo_actual'" />
                    <input type="hidden" name="remove_logo" id="removeLogoInput_logotipo_actual" value="0">
                </div>
            </div>
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <!-- Campo Nombre -->
                        <x-adminlte-input name="nombre" label="Nombre:"
                            value="{{ old('nombre', $ministerio->nombre ?? '') }}" />
                    </div>

                    <div class="col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Categoría:</label>
                            <x-adminlte-select2 name="tipo" class="form-control">
                                <option value="" disabled>Seleccione Categoría</option>
                                @foreach ([1 => 'Alto', 0 => 'Estandar'] as $key => $type)
                                    <option value="{{ $key }}" {{ old('tipo', $ministerio->tipo ?? 0) == $key ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </x-adminlte-select2>
                        </div>
                    </div>

                    <div class="col-md-12 col-lg-12">
                        <!-- Campo Líderes -->
                        <x-adminlte-select2 id="usuariosSelect" name="user_id[]" label="Líderes"
                            :config="array_merge($select2Config, ['placeholder' => 'Seleccione uno o varios líderes...'])" multiple>
                            @foreach ($lideres as $lider)
                                <option value="{{ $lider->id }}" {{ in_array($lider->id, old('user_id', $ministerio->lideres->pluck('id')->toArray() ?? [])) ? 'selected' : '' }}>
                                    {{ $lider->name }}
                                </option>
                            @endforeach
                        </x-adminlte-select2>
                    </div>
                </div>

            </div>
        </div>
        <!-- Botones de Acción -->
        <div class="d-flex justify-content-between">
            <x-adminlte-button class="btn w-100" type="submit"
                label="{{ isset($ministerio->id) ? 'Guardar cambios' : 'Guardar' }}" theme="success"
                icon="fas fa-lg fa-save" />
        </div>
    </form>
</x-adminlte-card>

@push('breadcrumb-plugins')
    <a href="{{ route('admin.ministerios.index') }}" class="btn btn-secondary rounded">
        <i class="fas fa-undo"></i> Regresar
    </a>
@endpush