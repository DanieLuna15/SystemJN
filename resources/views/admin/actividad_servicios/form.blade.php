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
            <x-adminlte-input name="nombre" label="Nombre:" value="{{ old('nombre', $actividad_servicio->nombre ?? '') }}" />
                
            <!-- Campo Imagen -->
            <div class="form-group">
                <label>@lang('Imagen')</label>
                <div class="image-upload">
                    <div class="thumb">
                        <div class="avatar-preview">
                            <div class="profilePicPreview" id="imagePreview"
                                style="background-size: contain !important; background-position: center !important; 
                                               background-image: url({{ isset($actividad_servicio->imagen) ? asset($actividad_servicio->imagen) : '' }}); 
                                               border-radius: 8px; width: 100%; height: 200px; position: relative; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                                <x-adminlte-button type="button" class="remove-image" id="removeImage"
                                    style="display: {{ isset($actividad_servicio->imagen) ? 'block' : 'none' }}; 
                                            position: absolute; top: 10px; right: 10px; background-color: rgba(0, 0, 0, 0.5); color: white; border: none; 
                                            border-radius: 50%; padding: 5px 10px;">
                                    <i class="fa fa-times"></i>
                                </x-adminlte-button>
                            </div>
                        </div>
                        <div class="avatar-edit mt-2">
                            <x-adminlte-input type="file" class="profilePicUpload" name="imagen" id="profilePicUpload1"
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
