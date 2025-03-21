<form action="{{ route('admin.perfil.updateImage', $usuario->id) }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <x-image-upload label="Imagen de perfil" name="profile_image"
        alt="Imagen de perfil del usuario" :image="$usuario->profile_image ?? null" :id="'imagen_perfil'" />

    <input type="hidden" name="remove_imagen" id="removeImagenInput_imagen_perfil"
        value="0">
    @can('editar perfil_imagen')
        <div class="d-flex justify-content-between mt-3">
            <x-adminlte-button class="btn w-100" type="submit" label="Guardar imagen"
                theme="success" icon="fas fa-lg fa-save" />
        </div>
    @endcan
</form>