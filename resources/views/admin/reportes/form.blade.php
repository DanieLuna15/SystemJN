<form action="{{ route('admin.reportes.multa') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-6 col-lg-6">
            <x-date-range-picker name="date_range" label="Rango de Fecha/Hora" :value="$dateRange" />
        </div>

        <div class="col-md-3 col-lg-3">
            <div class="form-group">
                <label>Ministerio:</label>
                <x-adminlte-select2 name="ministerio_id" class="form-control">
                    <option value="" selected disabled>Seleccione un ministerio</option>
                    @foreach ($ministerios as $ministerio)
                        <option value="{{ $ministerio->id }}"
                            {{ old('ministerio_id', $deptId) == $ministerio->id ? 'selected' : '' }}>
                            {{ $ministerio->dept_name }}
                        </option>
                    @endforeach
                </x-adminlte-select2>
            </div>
        </div>

        <div class="col-md-3 col-lg-3">
            <div class="form-group">
                <label>Ministerio Propio:</label>
                <x-adminlte-select2 name="ministerio_propio_id" class="form-control">
                    <option value="" selected disabled>Seleccione un ministerio</option>
                    @foreach ($ministeriosPropios as $ministerio)
                        <option value="{{ $ministerio->id }}"
                            {{ old('ministerio_propio_id', $ministerioId) == $ministerio->id ? 'selected' : '' }}>
                            {{ $ministerio->nombre }}
                        </option>
                    @endforeach
                </x-adminlte-select2>
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="d-flex justify-content-between">
        <x-adminlte-button class="btn w-100" type="submit" label="Consultar" theme="success"
            icon="fas fa-lg fa-search" />
    </div>
</form>
