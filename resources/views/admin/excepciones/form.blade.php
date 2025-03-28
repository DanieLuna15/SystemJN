@php
    $select2Config = config('select2');
@endphp

<x-adminlte-card>
    <form action="{{ route('admin.excepciones.save', $excepcion->id ?? null) }}" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{ $excepcion->id ?? '' }}">
        <div class="row">

            <!-- Campo Ministerio -->
            <div class="col-md-12 col-lg-12">
                <x-adminlte-select2 id="ministeriosSelect" name="ministerio_id[]" label="Ministerio(s)"
                    :config="array_merge($select2Config, ['placeholder' => 'Seleccione uno o varios ministerios...'])"
                    multiple>
                    @foreach ($ministerios as $ministerio)
                        <option value="{{ $ministerio->id }}" {{ in_array($ministerio->id, old('ministerio_id', $excepcion->ministerios->pluck('id')->toArray() ?? [])) ? 'selected' : '' }}>
                            {{ $ministerio->nombre }}
                        </option>
                    @endforeach
                </x-adminlte-select2>
            </div>

            <!-- Campo Fecha -->
            <div class="col-md-6 col-lg-6" id="fecha">
                <x-adminlte-input type="date" name="fecha" label="Fecha:" id="fechaInput" min="{{ date('Y-m-d') }}"
                    value="{{ old('fecha', $excepcion->fecha ?? '') }}">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <!-- Campo tipo de excepcion -->
            <div class="col-md-6 col-lg-6">
                <label>Tiempo de la excepcion:</label>
                <x-adminlte-select2 name="tipo" id="tiempoExcepcion" class="form-control">
                    <option value="" disabled selected>Selecciona un tipo de excepcion</option>
                    @foreach ([1 => 'Todo el dia', 0 => 'Rango de horas', 2 => 'Varios dias'] as $key => $type)
                        <option value="{{ $key }}" {{ old('tipo', $excepcion->dia_entero ?? 1) == $key ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </x-adminlte-select2>
            </div>

            <!-- Campo Fecha hasta (Oculto por defecto) -->
            <div class="col-md-6 col-lg-6" id="fechaContainer" style="display: none;">
                <x-adminlte-input type="date" name="hasta" label="Hasta:" id="fechaFin" min="{{ date('Y-m-d') }}"
                    value="{{ old('hasta', $excepcion->hasta ?? '') }}">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <!-- Campo Hora inicio (Oculto por defecto) -->
            <div class="col-md-6 col-lg-6" id="horaContainerInicio" style="display: none;">
                <x-adminlte-input type="time" name="hora_inicio" label="Hora Inicio:"
                    value="{{ old('hora_inicio', $excepcion->hora_inicio ?? '') }}" step="60">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="far fa-clock"></i>
                        </div>
                    </x-slot>
                    <x-slot name="bottomSlot">
                        <span class="text-sm text-gray">
                            [Registro de la hora de inicio de la excepcion.
                            (Formato:24hrs.)]
                        </span>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <!-- Campo Hora fin (Oculto por defecto) -->
            <div class="col-md-6 col-lg-6" id="horaContainerFin" style="display: none;">
                <x-adminlte-input type="time" name="hora_fin" label="Hora Fin:"
                    value="{{ old('hora_fin', $excepcion->hora_fin ?? '') }}" step="60">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="far fa-clock"></i>
                        </div>
                    </x-slot>
                    <x-slot name="bottomSlot">
                        <span class="text-sm text-gray">
                            [Registro de la hora fin de la excepcion.
                            (Formato:24hrs.)]
                        </span>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <div class="col-md-12 col-lg-12">
                <!-- Campo Motivo -->
                <x-adminlte-textarea name="motivo" label="Motivo:" fgroup-class="col-md-12" rows=3>
                    {{ old('motivo', $excepcion->motivo ?? '') }}
                </x-adminlte-textarea>
            </div>

        </div>

        <!-- Botones de Acción -->
        <div class="d-flex justify-content-between">
            <x-adminlte-button class="btn w-100" type="submit"
                label="{{ isset($excepcion->id) ? 'Guardar cambios' : 'Guardar' }}" theme="success"
                icon="fas fa-lg fa-save" />
        </div>
    </form>
</x-adminlte-card>

<!-- Script para mostrar/ocultar campos dinámicamente -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Función para mostrar/ocultar campos dinámicamente
        function toggleFields() {
            let tipoExcepcion = $('#tiempoExcepcion').val(); // Captura el valor seleccionado

            if (tipoExcepcion == "1") {
                // Selección "Todo el día" - Ocultar ambos contenedores
                $('#fechaContainer').hide();
                $('#horaContainerInicio').hide();
                $('#horaContainerFin').hide();
            } else if (tipoExcepcion == "0") {
                // Selección "Rango de horas" - Mostrar horas y ocultar fecha
                $('#fechaContainer').hide();
                $('#horaContainerInicio').show();
                $('#horaContainerFin').show();
            } else if (tipoExcepcion == "2") {
                // Selección "Varios días" - Mostrar fecha y ocultar horas
                $('#horaContainerInicio').hide();
                $('#horaContainerFin').hide();
                $('#fechaContainer').show();
            }
        }

        // Ejecutar al cargar la página (por si viene con un valor preseleccionado)
        toggleFields();

        // Evento para detectar cambios en el Select2
        $('#tiempoExcepcion').on('change', function () {
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
    <a href="{{ route('admin.horarios.index') }}" class="btn btn-secondary rounded">
        <i class="fas fa-undo"></i> Regresar
    </a>
@endpush