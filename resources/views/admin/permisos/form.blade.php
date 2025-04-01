@php
    $select2Config = config('select2');
@endphp

<x-adminlte-card>
    <form action="{{ route('admin.permisos.save', $permiso->id ?? null) }}" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{ $permiso->id ?? '' }}">
        <div class="row">

            <!-- Campo Usuario -->
            <div class="col-md-12 col-lg-12">
                <x-adminlte-select2 id="usuariosSelect" name="usuario_id[]" label="Usuario(s)"
                    :config="array_merge($select2Config, ['placeholder' => 'Seleccione uno o varios usuarios...'])"
                    multiple>
                    @foreach ($usuarios as $usuario)
                        <option value="{{ $usuario->id }}" {{ in_array($usuario->id, old('usuario_id', $permiso->usuarios->pluck('id')->toArray() ?? [])) ? 'selected' : '' }}>
                            {{ $usuario->name }} {{ $usuario->last_name }}
                        </option>
                    @endforeach
                </x-adminlte-select2>
            </div>

            <!-- Campo Fecha -->
            <div class="col-md-6 col-lg-6" id="fecha">
                <x-adminlte-input type="date" name="fecha" label="Fecha:" id="fechaInput"
                    value="{{ old('fecha', $permiso->fecha ?? '') }}">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <!-- Campo tipo de permiso -->
            <div class="col-md-6 col-lg-6">
                <label>Tiempo del permiso:</label>
                <x-adminlte-select2 name="tipo" id="tiempoPermiso" class="form-control">
                    <option value="" disabled selected>Selecciona un tipo de permiso</option>
                    @foreach ([1 => 'Todo el día', 0 => 'Rango de horas', 2 => 'Varios días'] as $key => $type)
                        <option value="{{ $key }}" {{ old('tipo', $permiso->dia_entero ?? 1) == $key ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </x-adminlte-select2>
            </div>

            <!-- Campo Fecha hasta (Oculto por defecto) -->
            <div class="col-md-6 col-lg-6" id="fechaContainer" style="display: none;">
                <x-adminlte-input type="date" name="hasta" label="Hasta:" id="fechaFin"
                    value="{{ old('hasta', $permiso->hasta ?? '') }}">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <!-- Campo Hora inicio (Oculto por defecto)-->
            <div class="col-md-6 col-lg-6" id="horaContainerInicio" style="display: none;">
                <x-adminlte-input type="time" name="hora_inicio" label="Hora Inicio:"
                    value="{{ old('hora_inicio', $permiso->hora_inicio ?? '') }}" step="60">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="far fa-clock"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <!-- Campo Hora fin (Oculto por defecto) -->
            <div class="col-md-6 col-lg-6" id="horaContainerFin" style="display: none;">
                <x-adminlte-input type="time" name="hora_fin" label="Hora Fin:"
                    value="{{ old('hora_fin', $permiso->hora_fin ?? '') }}" step="60">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="far fa-clock"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <div class="col-md-12 col-lg-12">
                <!-- Campo Motivo -->
                <x-adminlte-textarea name="motivo" label="Motivo:" fgroup-class="col-md-12" rows=3>
                    {{ old('motivo', $permiso->motivo ?? '') }}
                </x-adminlte-textarea>
            </div>

        </div>

        <!-- Botón de acción -->
        <div class="d-flex justify-content-between">
            <x-adminlte-button class="btn w-100" type="submit"
                label="{{ isset($permiso->id) ? 'Guardar cambios' : 'Guardar' }}" theme="success"
                icon="fas fa-lg fa-save" />
        </div>
    </form>
</x-adminlte-card>

<!-- Script para mostrar/ocultar campos dinámicamente -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        function toggleFields() {
            let tipoPermiso = $('#tiempoPermiso').val();

            if (tipoPermiso == "1") {
                $('#fechaContainer').hide();
                $('#horaContainerInicio').hide();
                $('#horaContainerFin').hide();
            } else if (tipoPermiso == "0") {
                $('#fechaContainer').hide();
                $('#horaContainerInicio').show();
                $('#horaContainerFin').show();
            } else if (tipoPermiso == "2") {
                $('#horaContainerInicio').hide();
                $('#horaContainerFin').hide();
                $('#fechaContainer').show();
            }
        }

        toggleFields();
        $('#tiempoPermiso').on('change', function () {
            toggleFields();
        });

        // Lista de IDs de los inputs
        const fechaInputs = ["fechaInput", "fechaInicio", "fechaFin"]; // Agrega todos los IDs que deseas manejar

        // Iterar sobre los IDs y agregar el evento focus a cada input
        fechaInputs.forEach(function (id) {
            const fechaInput = document.getElementById(id); // Obtiene el input por su ID
            if (fechaInput) {
                fechaInput.addEventListener("focus", function () {
                    // Abre el selector de fechas al hacer clic en el input
                    this.showPicker && this.showPicker(); // Verifica que el navegador soporte 'showPicker'
                });
            }
        });
    });
</script>

@push('breadcrumb-plugins')
    <a href="{{ route('admin.permisos.index') }}" class="btn btn-secondary rounded">
        <i class="fas fa-undo"></i> Regresar
    </a>
@endpush
