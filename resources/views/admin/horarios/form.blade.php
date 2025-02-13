<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.horarios.save', $horario->id ?? null) }}" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{ $horario->id ?? '' }}">
            
            <div class="form-group">
                <label>Tipo:</label>
                <x-adminlte-select2 name="tipo" class="form-control">
                    <option value="" selected disabled>Seleccione el tipo de horario</option>
                    @foreach ([1 => 'Fijo', 0 => 'Eventual'] as $key => $type)
                        <option value="{{ $key }}"
                            {{ old('tipo', $horario->tipo ?? '') == $key ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </x-adminlte-select2>
            </div>

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

            <div class="form-group">
                <label>Hora de Registro:</label>
                <x-adminlte-input type="time" name="hora_registro" class="form-control"
                    value="{{ old('hora_registro', $horario->hora_registro ?? '') }}">
                </x-adminlte-input>
               
            </div>

            <!-- Campo Hora Multa -->
            <x-adminlte-input type="time" name="hora_multa" label="Hora de Multa:" value="{{ old('hora_multa', $horario->hora_multa ?? '') }}" />


            <button type="submit" class="btn btn-success">
                {{ isset($horario->id) ? 'Actualizar' : 'Guardar' }}
            </button>

            <a href="{{ route('admin.horarios.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
