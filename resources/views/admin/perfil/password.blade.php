<form method="POST" action="{{ route('admin.perfil.updatePassword', auth()->id()) }}">
    @csrf
    @method('PUT')
    <div class="col-md-12 col-lg-12">
        <x-adminlte-input name="password_actual" label="Contraseña Actual:" type="password"
            placeholder="Ingrese su contraseña actual" required fgroup-class="input-group">
            <x-slot name="appendSlot">
                <button class="btn btn-primary" type="button"
                    onclick="togglePassword('password_actual', 'eyeIcon_actual')">
                    <i id="eyeIcon_actual" class="fa fa-eye"></i>
                </button>
            </x-slot>
        </x-adminlte-input>

    </div>
    <div class="col-md-12 col-lg-12">
        <x-adminlte-input name="password" label="Nueva Contraseña:" type="password"
            placeholder="Ingrese su nueva contraseña" required fgroup-class="input-group">
            <x-slot name="appendSlot">
                <button class="btn btn-primary" type="button"
                    onclick="togglePassword('password', 'eyeIcon_new')">
                    <i id="eyeIcon_new" class="fa fa-eye"></i>
                </button>
            </x-slot>
        </x-adminlte-input>
    </div>
    <div class="col-md-12 col-lg-12">
        <x-adminlte-input name="password_confirmation" label="Confirmar Nueva Contraseña:"
            type="password" placeholder="Confirme su nueva contraseña" required
            fgroup-class="input-group">
            <x-slot name="appendSlot">

                <button class="btn btn-primary" type="button"
                    onclick="togglePassword('password_confirmation', 'eyeIcon_confirmation')">
                    <i id="eyeIcon_confirmation" class="fa fa-eye"></i>
                </button>
            </x-slot>
        </x-adminlte-input>
    </div>
    @can('editar perfil_contraseña')
        <div class="d-flex justify-content-between mt-3">
            <x-adminlte-button class="btn w-100" type="submit" label="Actualizar contraseña"
                theme="success" icon="fas fa-lg fa-save" />
        </div>
    @endcan
</form>
