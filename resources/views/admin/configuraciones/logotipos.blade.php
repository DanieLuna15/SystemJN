<form action="{{ route('admin.configuracion.update_logo', $configuracion->id) }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    @method('PUT') <!-- Esto asegura que se use el método PUT -->

    <div class="row">
        <div class="col-lg-6">
            <!-- Logotipo actual -->
            <x-image-upload label="Logotipo actual" name="logo" alt="Logotipo del sistema"
                :image="$configuracion->logo ?? null" :id="'logotipo_actual'" />
                <input type="hidden" name="remove_logo" id="removeLogoInput_logotipo_actual" value="0">
        </div>

        <div class="col-lg-6">
            <!-- Favicon actual -->
            <x-image-upload label="Favicon actual" name="favicon" alt="Favicon del sistema"
                :image="$configuracion->favicon ?? null" :id="'favicon_actual'" />
                <input type="hidden" name="remove_favicon" id="removeLogoInput_favicon_actual" value="0">
        </div>
    </div>

    <!-- Botón de Acción -->
    <div class="d-flex justify-content-between">
        <x-adminlte-button class="btn w-100" type="submit" label="Guardar Cambios" theme="success"
            icon="fas fa-lg fa-save" />
    </div>
</form>
