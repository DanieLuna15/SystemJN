<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.horarios.save', $horario->id ?? null) }}" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{ $horario->id ?? '' }}">
            <div class="row">

                <div class="col-md-6 col-lg-6">
                    <div class="form-group">
                        <label>Clase:</label>
                        <x-adminlte-select2 name="tipo" class="form-control">
                            <option value="" disabled>Seleccione la clase de horario</option>
                            @foreach ([1 => 'Fijo', 0 => 'Eventual'] as $key => $type)
                                <option value="{{ $key }}"
                                    {{ old('tipo', $horario->tipo ?? 1) == $key ? 'selected' : '' }}>
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
                                <option value="{{ $actividadServicio->id }}"
                                    {{ old('actividad_servicio_id', $horario->actividad_servicio_id ?? '') == $actividadServicio->id ? 'selected' : '' }}>
                                    {{ $actividadServicio->nombre }}
                                </option>
                            @endforeach
                        </x-adminlte-select2>
                    </div>
                </div>

                <div class="col-md-12 col-lg-12">
                    <div class="form-group">
                        <label>Ministerio:</label>
                        <x-adminlte-select2 name="ministerio_id" class="form-control">
                            <option value="" selected disabled>Seleccione un ministerio</option>
                            @foreach ($ministerios as $ministerio)
                                <option value="{{ $ministerio->id }}"
                                    {{ old('ministerio_id', $horario->ministerio_id ?? '') == $ministerio->id ? 'selected' : '' }}>
                                    {{ $ministerio->nombre }}
                                </option>
                            @endforeach
                        </x-adminlte-select2>
                    </div>
                </div>
                <div class="col-md-12 col-lg-12">
                    <div class="form-group">
                        <label>Día de la Semana:</label>
                        <x-adminlte-select2 name="dia_semana" class="form-control">
                            <option value="" selected disabled>Seleccione un día</option>
                            @foreach ([1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'] as $key => $day)
                                <option value="{{ $key }}"
                                    {{ old('dia_semana', $horario->dia_semana ?? '') == $key ? 'selected' : '' }}>
                                    {{ $day }}
                                </option>
                            @endforeach
                        </x-adminlte-select2>
                    </div>
                </div>

                <div class="col-md-6 col-lg-6">
                    <!-- Campo Hora Registro -->
                    <x-adminlte-input type="time" name="hora_registro" label="Hora de Registro:"
                        value="{{ old('hora_registro', $horario->hora_registro ?? '') }}">
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
                <div class="col-md-6 col-lg-6">
                    <!-- Campo Hora Multa -->
                    <x-adminlte-input type="time" name="hora_multa" label="Hora de Multa:"
                        value="{{ old('hora_multa', $horario->hora_multa ?? '') }}">
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
            </div>
            <!-- Botones de Acción -->
            <div class="d-flex justify-content-between">
                <x-adminlte-button class="btn" type="submit"
                    label="{{ isset($horario->id) ? 'Actualizar' : 'Guardar' }}" theme="success"
                    icon="fas fa-lg fa-save" />
                <a href="{{ route('admin.horarios.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>




