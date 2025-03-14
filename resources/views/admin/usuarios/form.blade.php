@php
    $select2Config = config('select2');
@endphp

<x-adminlte-card>
    <form action="{{ route('admin.usuarios.save', $usuario->id ?? null) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{ $usuario->id ?? '' }}">
        <div class="row">
            <div class="col-lg-4">
                <div class="col-sm-12">
                    <!-- Campo Imagen -->
                    <x-image-upload 
                        label="Imagen" 
                        name="profile_image" 
                        :image="old('profile_image', $errors->any() ? null : ($usuario->profile_image ?? null))" 
                        :id="'logotipo_actual'" 
                    />
                    <input type="hidden" name="remove_logo" id="removeLogoInput_logotipo_actual" value="{{ old('remove_logo', 0) }}">
                </div>                
                
            </div>
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <!-- Campo Nombre -->
                        <x-adminlte-input name="name" label="Nombre:" value="{{ old('name', $usuario->name ?? '') }}" />
                    </div>

                    <div class="col-md-6 col-lg-6">
                        <!-- Campo Apellido -->
                        <x-adminlte-input name="last_name" label="Apellido:"
                            value="{{ old('last_name', $usuario->last_name ?? '') }}" />
                    </div>

                    <div class="col-md-6 col-lg-6">
                        <!-- Campo CI -->
                        <x-adminlte-input name="ci" label="CI:" value="{{ old('ci', $usuario->ci ?? '') }}" />
                    </div>

                    <div class="col-md-6 col-lg-6">
                        <!-- Campo Roles -->
                        <x-adminlte-select2 id="rolesSelect" name="rol_id" label="Rol:">
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ old('rol_id', $usuario->roles->first()->id ?? 3) == $role->id ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </x-adminlte-select2>

                    </div>


                    <div class="col-md-6 col-lg-6">
                        <!-- Campo Correo -->
                        <x-adminlte-input name="email" label="Correo:"
                            value="{{ old('email', $usuario->email ?? '') }}" />
                    </div>
                    <div class="col-md-6 col-lg-6">
                        <!-- Campo Teléfono -->
                        <x-adminlte-input name="phone" label="Teléfono:"
                            value="{{ old('phone', $usuario->phone ?? '') }}" />
                    </div>



                    <div class="col-md-12 col-lg-12">
                        <!-- Campo Ministerios -->
                        <x-adminlte-select2 id="ministeriosSelect" name="ministerio_id[]" label="Ministerios:"
                            :config="array_merge($select2Config, ['placeholder' => 'Seleccione uno o varios ministerios...'])" multiple>
                            @foreach ($ministerios as $ministerio)
                                <option value="{{ $ministerio->id }}" {{ in_array($ministerio->id, old('ministerio_id', $usuario->ministerios->pluck('id')->toArray() ?? [])) ? 'selected' : '' }}>
                                    {{ $ministerio->nombre }}
                                </option>
                            @endforeach
                        </x-adminlte-select2>
                    </div>

                    <div class="col-md-12 col-lg-12">
                        <!-- Campo Direccion -->
                        <x-adminlte-input name="address" label="Dirección:"
                            value="{{ old('address', $usuario->address ?? '') }}" />
                    </div>


                </div>

            </div>
        </div>
        <!-- Botones de Acción -->
        <div class="d-flex justify-content-between">
            <x-adminlte-button class="btn w-100" type="submit"
                label="{{ isset($usuario->id) ? 'Guardar cambios' : 'Guardar' }}" theme="success"
                icon="fas fa-lg fa-save" />
        </div>
    </form>
</x-adminlte-card>

@push('breadcrumb-plugins')
    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary rounded">
        <i class="fas fa-undo"></i> Regresar
    </a>
@endpush