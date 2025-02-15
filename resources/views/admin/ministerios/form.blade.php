<x-adminlte-card>
    <x-slot name="header">
        <h3 class="card-title">{{ isset($ministerio->id) ? 'Actualizar Ministerio' : 'Nuevo Ministerio' }}</h3>
    </x-slot>

    <div class="card-body">
        <form action="{{ route('admin.ministerios.save', $ministerio->id ?? null) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{ $ministerio->id ?? '' }}">
            <div class="form-group">
                <label>Tipo de Ministerio:</label>
                <x-adminlte-select2 name="tipo" class="form-control">
                    <option value="" selected disabled>Seleccione Tipo</option>
                    @foreach ([1 => 'Alto', 0 => 'Estandar'] as $key => $type)
                        <option value="{{ $key }}"
                            {{ old('tipo', $ministerio->tipo?? '') == $key ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </x-adminlte-select2>
            </div>
            <!-- Campo Nombre -->
            <x-adminlte-input name="nombre" label="Nombre:" value="{{ old('nombre', $ministerio->nombre ?? '') }}" />

            <!-- Campo Imagen -->

            <div class="form-group">
                <label>@lang('Imagen')</label>
                <div class="image-upload">
                    <div class="thumb">
                        <div class="avatar-preview">
                            <div class="profilePicPreview" id="imagePreview"
                                style="background-size: contain !important; background-position: center !important;
                                               background-image: url({{ isset($ministerio->logo) ? asset($ministerio->logo) : '' }});
                                               border-radius: 8px; width: 100%; height: 200px; position: relative; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                                <x-adminlte-button type="button" class="remove-image" id="removeImage"
                                    style="display: {{ isset($ministerio->logo) ? 'block' : 'none' }};
                                            position: absolute; top: 10px; right: 10px; background-color: rgba(0, 0, 0, 0.5); color: white; border: none;
                                            border-radius: 50%; padding: 5px 10px;">
                                    <i class="fa fa-times"></i>
                                </x-adminlte-button>
                            </div>
                        </div>
                        <div class="avatar-edit mt-2">
                            <x-adminlte-input type="file" class="profilePicUpload" name="logo" id="profilePicUpload1"
                                accept=".png, .jpg, .jpeg" onchange="previewImage(event)" style="display: none;"/>
                            <label for="profilePicUpload1" class="btn btn-success btn-block btn-lg"
                                style="border-radius: 8px; font-size: 16px; padding: 12px 20px;">
                                @lang('Subir Imagen')
                            </label>
                            <small class="d-block text-center text-muted mt-2">@lang('Soporta imágenes')
                                <b>@lang('jpeg'), @lang('jpg'), @lang('png')</b></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campo Multa por Retraso -->
            <x-adminlte-input name="multa_incremento" label="Multa por Retraso (Bs):" type="number" step="0.01"
                value="{{ old('multa_incremento', $ministerio->multa_incremento ?? '') }}">
                <x-slot name="prependSlot">
                    <div class="input-group-text">
                        <b>Bs.</b>
                    </div>
                </x-slot>
                <x-slot name="bottomSlot">
                    <span class="text-sm text-gray">
                        [Este es el monto de multa acumulativa.]
                    </span>
                </x-slot>
            </x-adminlte-input>

            <!-- Botones de Acción -->
            <div class="d-flex justify-content-between">
                <x-adminlte-button class="btn" type="submit"
                    label="{{ isset($ministerio->id) ? 'Actualizar' : 'Guardar' }}" theme="success"
                    icon="fas fa-lg fa-save" />
                <a href="{{ route('admin.ministerios.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</x-adminlte-card>


<script>
    // Función para previsualizar la imagen antes de cargarla
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('imagePreview');
            const removeBtn = document.getElementById('removeImage');
            output.style.backgroundImage = 'url(' + reader.result + ')';
            output.style.backgroundSize = 'contain';
            output.style.backgroundPosition = 'center';
            removeBtn.style.display = 'block'; // Mostrar el botón de eliminar
        }
        reader.readAsDataURL(event.target.files[0]);
    }

    // Función para eliminar la imagen
    document.getElementById('removeImage').addEventListener('click', function() {
        const output = document.getElementById('imagePreview');
        const removeBtn = document.getElementById('removeImage');
        output.style.backgroundImage = ''; // Eliminar la imagen previa
        removeBtn.style.display = 'none'; // Ocultar el botón de eliminar
        document.getElementById('profilePicUpload1').value = ''; // Limpiar el campo de carga
    });
</script>
