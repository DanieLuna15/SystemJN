<form action="{{ route('admin.configuracion.update', $configuracion->id) }}" method="POST">
    @csrf
    @method('PUT') <!-- Esto asegura que se use el método PUT -->
    <div class="row">

        <div class="col-md-6 col-lg-6">
            <!-- Campo Nombre -->
            <x-adminlte-input name="nombre" label="Nombre:" value="{{ old('nombre', $configuracion->nombre ?? '') }}" />
        </div>

        <div class="col-md-6 col-lg-6">
            <!-- Campo Email -->
            <x-adminlte-input name="email" label="Email:" value="{{ old('email', $configuracion->email ?? '') }}" />
        </div>

        <div class="col-md-12 col-lg-12">
            <!-- Campo Descripcion -->
            <x-adminlte-textarea name="descripcion" label="Descripción:" fgroup-class="col-md-12" rows=3>
                {{ old('descripcion', $configuracion->descripcion ?? '') }}
            </x-adminlte-textarea>
        </div>

        <div class="col-md-12 col-lg-12">
            <!-- Campo Direccion -->
            <x-adminlte-input name="direccion" label="Dirección:"
                value="{{ old('direccion', $configuracion->direccion ?? '') }}" />
        </div>

        <div class="col-md-6 col-lg-6">
            <!-- Campo Telefono -->
            <x-adminlte-input name="telefono" label="Telefono:"
                value="{{ old('telefono', $configuracion->telefono ?? '') }}" />
        </div>

        <div class="col-md-6 col-lg-6">
            <!-- Campo Url -->
            <x-adminlte-input name="url" label="Url:" value="{{ old('url', $configuracion->url ?? '') }}" />
        </div>

    </div>

    <!-- Botones de Acción -->
    <div class="d-flex justify-content-between">
        <x-adminlte-button class="btn w-100" type="submit"
            label="Guardar cambios" theme="success"
            icon="fas fa-lg fa-save" />
    </div>
</form>
