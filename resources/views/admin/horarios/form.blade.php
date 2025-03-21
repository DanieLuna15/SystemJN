@php
    $select2Config = config('select2');
@endphp

<x-adminlte-card>
    <form action="{{ route('admin.horarios.save', $horario->id ?? null) }}" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{ $horario->id ?? '' }}">
        <div class="row">

            <div class="col-md-6 col-lg-6">
                <div class="form-group">
                    <label>Tipo:</label>
                    <x-adminlte-select2 name="tipo" id="tipoHorario" class="form-control">
                        <option value="" disabled>Seleccione el tipo de horario</option>
                        @foreach ([1 => 'Fijo', 0 => 'Eventual'] as $key => $type)
                            <option value="{{ $key }}" {{ old('tipo', $horario->tipo ?? 1) == $key ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </x-adminlte-select2>
                </div>
            </div>

            <div class="col-md-6 col-lg-6">
                <div class="form-group">
                    <label>Actividad o Servicio:</label>
                    <x-adminlte-select2 name="actividad_servicio_id" class="form-control">
                        <option value="" selected disabled>Seleccione una actividad o servicio</option>
                        @foreach ($actividadServicios as $actividadServicio)
                            <option value="{{ $actividadServicio->id }}" {{ old('actividad_servicio_id', $horario->actividad_servicio_id ?? '') == $actividadServicio->id ? 'selected' : '' }}>
                                {{ $actividadServicio->nombre }}
                            </option>
                        @endforeach
                    </x-adminlte-select2>
                </div>
            </div>

            <div class="col-md-12 col-lg-12">
                <x-adminlte-select2 id="ministeriosSelect" name="ministerio_id[]" label="Ministerios"
                    :config="array_merge($select2Config, ['placeholder' => 'Seleccione uno o varios ministerios...'])"
                    multiple>
                    @foreach ($ministerios as $ministerio)
                        <option value="{{ $ministerio->id }}" {{ in_array($ministerio->id, old('ministerio_id', $horario->ministerios->pluck('id')->toArray() ?? [])) ? 'selected' : '' }}>
                            {{ $ministerio->nombre }}
                        </option>
                    @endforeach
                </x-adminlte-select2>
            </div>

            <!-- Día de la Semana -->
            <div class="col-md-12 col-lg-12" id="diaSemanaContainer">
                <div class="form-group">
                    <label>Día de la Semana:</label>
                    <x-adminlte-select2 name="dia_semana" class="form-control">
                        <option value="" selected disabled>Seleccione un día</option>
                        @foreach ([1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 0 => 'Domingo'] as $key => $day)
                            <option value="{{ $key }}" {{ old('dia_semana', $horario->dia_semana ?? '') == $key ? 'selected' : '' }}>
                                {{ $day }}
                            </option>
                        @endforeach
                    </x-adminlte-select2>
                </div>
            </div>

            <!-- Campo Fecha (Oculto por defecto) -->
            <div class="col-md-12 col-lg-12" id="fechaContainer" style="display: none;">
                <x-adminlte-input type="date" name="fecha" label="Fecha:" id="fechaInput" min="{{ date('Y-m-d') }}"
                    value="{{ old('fecha', $horario->fecha ?? '') }}">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <div class="col-md-4 col-lg-4">
                <x-adminlte-input type="time" name="hora_registro" label="Hora de Registro:"
                    value="{{ old('hora_registro', $horario->hora_registro ?? '') }}" step="1">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="far fa-clock"></i>
                        </div>
                    </x-slot>
                    <x-slot name="bottomSlot">
                        <span class="text-sm text-gray">
                            [La hora desde la cual se puede marcar la asistencia en el lector biométrico.
                            (Formato:24hrs.)]
                        </span>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <div class="col-md-4 col-lg-4">
                <x-adminlte-input type="time" name="hora_multa" label="Hora de Multa:"
                    value="{{ old('hora_multa', $horario->hora_multa ?? '') }}" step="1">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="far fa-clock"></i>
                        </div>
                    </x-slot>
                    <x-slot name="bottomSlot">
                        <span class="text-sm text-gray">
                            [La hora desde la cual empieza a correr la multa. (Formato:24hrs.)]
                        </span>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <div class="col-md-4 col-lg-4">
                <x-adminlte-input type="time" name="hora_limite" label="Hora Límite:"
                    value="{{ old('hora_limite', $horario->hora_limite ?? '') }}" step="1">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="far fa-clock"></i>
                        </div>
                    </x-slot>
                    <x-slot name="bottomSlot">
                        <span class="text-sm text-gray">
                            [La hora límite para marcar la asistencia. (Formato:24hrs.)]
                        </span>
                    </x-slot>
                </x-adminlte-input>
            </div>
            
        </div>

        <!-- Botones de Acción -->
        <div class="d-flex justify-content-between">
            <x-adminlte-button class="btn w-100" type="submit"
                label="{{ isset($horario->id) ? 'Guardar cambios' : 'Guardar' }}" theme="success"
                icon="fas fa-lg fa-save" />
        </div>
    </form>
</x-adminlte-card>

<!-- Script para mostrar/ocultar campos dinámicamente -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        function toggleFields() {
            let tipoHorario = $('#tipoHorario').val(); // Captura el valor seleccionado
            if (tipoHorario == "0") { // Si es "Eventual"
                $('#fechaContainer').show(); // Muestra el campo "Fecha"
                $('#diaSemanaContainer').hide(); // Oculta el campo "Día de la Semana"
            } else {
                $('#fechaContainer').hide(); // Oculta el campo "Fecha"
                $('#diaSemanaContainer').show(); // Muestra el campo "Día de la Semana"
            }
        }

        // Ejecutar al cargar la página (por si viene con un valor preseleccionado)
        toggleFields();

        // Evento para detectar cambios en el Select2
        $('#tipoHorario').on('change', function () {
            toggleFields();
        });

        const fechaInput = document.getElementById("fechaInput");
        if (fechaInput) {
            fechaInput.addEventListener("focus", function () {
                this.showPicker(); // Abre el selector de fechas al hacer clic en el input
            });
        }
    });
</script>
@push('breadcrumb-plugins')
    <a href="{{ route('admin.horarios.index') }}" class="btn btn-secondary rounded">
        <i class="fas fa-undo"></i> Regresar
    </a>
@endpush