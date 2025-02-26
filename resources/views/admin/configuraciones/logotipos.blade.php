<div class="col-lg-12">
    <div class="callout callout-warning">
        <small>Si el logotipo, favicon o loader no cambian tras la actualización, <span class="text-danger">borre la caché del navegador</span>. Si persiste, limpie la caché del servidor o la red.</small>
    </div>
</div>
<form action="{{ route('admin.configuracion.update_logo', $configuracion->id) }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    @method('PUT') <!-- Esto asegura que se use el método PUT -->

    <div class="row">
        <div class="col-lg-4">
            <!-- Logotipo actual -->
            <x-image-upload label="Logotipo actual" name="logo" alt="Logotipo del sistema" :image="$configuracion->logo ?? null"
                :id="'logotipo_actual'" />
            <input type="hidden" name="remove_logo" id="removeLogoInput_logotipo_actual" value="0">
        </div>

        <div class="col-lg-4">
            <!-- Favicon actual -->
            <x-image-upload label="Favicon actual" name="favicon" alt="Favicon del sistema" :image="$configuracion->favicon ?? null"
                :id="'favicon_actual'" />
            <input type="hidden" name="remove_favicon" id="removeLogoInput_favicon_actual" value="0">
        </div>

        <div class="col-lg-4">
            <!-- Loader actual -->
            <x-image-upload label="Loader actual" name="loader" alt="Loader del sistema" :image="$configuracion->loader ?? null"
                :id="'loader_actual'" />
            <input type="hidden" name="remove_loader" id="removeLogoInput_loader_actual" value="0">
        </div>
    </div>

    <!-- Botón de Acción -->
    <div class="d-flex justify-content-between">
        <x-adminlte-button class="btn w-100" type="submit" label="Guardar Cambios" theme="success"
            icon="fas fa-lg fa-save" />
    </div>
</form>
