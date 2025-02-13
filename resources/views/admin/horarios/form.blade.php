<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.horarios.save', $horario->id ?? null) }}" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{ $horario->id ?? '' }}">

            <div class="form-group">
                <label>Ministerio:</label>
                <select name="ministerio_id" class="form-control">
                    <option value="" selected disabled>Seleccione un ministerio</option>
                    @foreach ($ministerios as $ministerio)
                        <option value="{{ $ministerio->id }}"
                            {{ old('ministerio_id', $horario->ministerio_id ?? '') == $ministerio->id ? 'selected' : '' }}>
                            {{ $ministerio->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('ministerio_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
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
                <input type="time" name="hora_registro" class="form-control"
                    value="{{ old('hora_registro', $horario->hora_registro ?? '') }}">
                @error('hora_registro')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Hora de Multa:</label>
                <input type="time" name="hora_multa" class="form-control"
                    value="{{ old('hora_multa', $horario->hora_multa ?? '') }}">
                @error('hora_multa')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>


            <button type="submit" class="btn btn-success">
                {{ isset($horario->id) ? 'Actualizar' : 'Guardar' }}
            </button>

            <a href="{{ route('admin.horarios.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
