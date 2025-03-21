<form action="{{ route('admin.perfil.update', $usuario->id) }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="form_type" value="secundario">

    <div class="row">
        @foreach ([
            'name' => 'Nombre',
            'last_name' => 'Apellido',
            'ci' => 'CI',
            'email' => 'Email',
            'phone' => 'Teléfono',
        ] as $field => $label)
            <div class="col-md-6">
                <x-adminlte-input name="{{ $field }}" label="{{ $label }}:"
                    value="{{ old($field, $usuario->$field) }}"
                    placeholder="Ingrese su {{ strtolower($label) }}" />
            </div>
        @endforeach

        <div class="col-md-12">
            <x-adminlte-textarea name="address" label="Dirección:" rows="3"
                placeholder="Ingrese su dirección">{{ old('address', $usuario->address) }}</x-adminlte-textarea>
        </div>
    </div>

    @can('editar perfil_informacion')
        <div class="d-flex justify-content-between mt-3">
            <x-adminlte-button class="btn w-100" type="submit" label="Guardar cambios"
                theme="success" icon="fas fa-lg fa-save" block />
        </div>
    @endcan
</form>