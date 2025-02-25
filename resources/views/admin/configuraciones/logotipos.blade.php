<div class="card-body">
    <div class="row">

        <div class="col-lg-6">
            <!-- Logotipo actual -->
            <x-image-upload label="Logotipo actual" name="logotipo" alt="Logotipo del sistema" :image="$configuracion->logo ?? null" :id="'logotipo_actual'" />
        </div>

        <div class="col-lg-6">
            <!-- Favicon actual -->
            <x-image-upload label="Favicon actual" name="favicon" alt="Favicon del sistema" :image="$configuracion->favicon ?? null" :id="'favicon_actual'" />
        </div>

    </div>

    <!-- Botones de Acción -->
    <div class="d-flex justify-content-between">
        <x-adminlte-button class="btn" type="submit" label="{{ isset($horario->id) ? 'Actualizar' : 'Guardar' }}"
            theme="success" icon="fas fa-lg fa-save" />
        <a href="{{ route('admin.horarios.index') }}" class="btn btn-secondary">Cancelar</a>
    </div>
</div>
