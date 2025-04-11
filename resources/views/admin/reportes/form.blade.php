<div class="row">
    <div class="col-md-6 col-lg-6">
        <x-date-range-picker name="date_range" label="Rango de Fecha/Hora" :value="$dateRange" />
    </div>


    <div class="col-md-6 col-lg-6">
        <div class="form-group">
            <label>Ministerio Propio:</label>
            <x-adminlte-select2 name="ministerio_id" class="form-control">
                <option value="" selected disabled>Seleccione un ministerio</option>
                @foreach ($ministerios as $ministerio)
                    <option value="{{ $ministerio->id }}"
                        {{ old('ministerio_id', $ministerioId) == $ministerio->id ? 'selected' : '' }}>
                        {{ $ministerio->nombre }}
                    </option>
                @endforeach
            </x-adminlte-select2>
        </div>
    </div>
</div>

<!-- Botones de Acción -->
<div class="d-flex justify-content-between">
    <x-adminlte-button class="btn w-100" type="submit" label="Consultar" theme="success" icon="fas fa-lg fa-search" />
</div>
