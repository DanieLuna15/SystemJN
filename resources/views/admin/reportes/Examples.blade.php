<form action="{{ route('admin.reporte.index') }}" method="POST">
    @csrf
    <div class="row">
        <!-- Campo Fecha (Oculto por defecto) 
        {{-- Minimal --}}
        {{-- <x-adminlte-date-range name="drBasic" /> --}}

        {{-- Disabled with predefined config --}}
        {{-- @php
        $config = [
        'timePicker' => true,
        'startDate' => "js:moment().subtract(6, 'days')",
        'endDate' => 'js:moment()',
        'locale' => ['format' => 'YYYY-MM-DD HH:mm'],
        ];
        @endphp
        <x-adminlte-date-range name="drDisabled" :config="$config" disabled /> --}}

        {{-- Prepend slot and custom ranges enables --}}
        {{-- <x-adminlte-date-range name="drCustomRanges" enable-default-ranges="Last 30 Days">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-gradient-info">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </x-slot>
        </x-adminlte-date-range> --}}

        {{-- Label and placeholder --}}
        {{-- <x-adminlte-date-range name="drPlaceholder" placeholder="Select a date range..." label="Date Range">
            <x-slot name="prependSlot">
                <div class="input-group-text bg-gradient-danger">
                    <i class="far fa-lg fa-calendar-alt"></i>
                </div>
            </x-slot>
        </x-adminlte-date-range>
        @push('js')
        <script>
            $(() => $("#drPlaceholder").val(''))
        </script>
        @endpush --}}

        {{-- SM size with single date/time config --}}
        {{-- @php
        $config = [
        'singleDatePicker' => true,
        'showDropdowns' => true,
        'startDate' => 'js:moment()',
        'minYear' => 2000,
        'maxYear' => "js:parseInt(moment().format('YYYY'),10)",
        'timePicker' => true,
        'timePicker24Hour' => true,
        'timePickerSeconds' => true,
        'cancelButtonClasses' => 'btn-danger',
        'locale' => ['format' => 'YYYY-MM-DD HH:mm:ss'],
        ];
        @endphp
        <x-adminlte-date-range name="drSizeSm" label="Date/Time" igroup-size="sm" :config="$config">
            <x-slot name="appendSlot">
                <div class="input-group-text bg-dark">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </x-slot>
        </x-adminlte-date-range> --}}

        {{-- LG size with some config and add-ons --}}
        @php
            $config = [
                'showDropdowns' => true,
                'startDate' => 'js:moment().startOf("month")', // Primer día del mes actual a las 00:00:00
                'endDate' => 'js:moment().endOf("month")', // Último día del mes actual a las 23:59:59
                'minYear' => 2000,
                'maxYear' => "js:parseInt(moment().format('YYYY'),10)",
                'timePicker' => true,
                'timePicker24Hour' => true,
                'timePickerSeconds' => true,
                'locale' => [
                    'format' => 'DD-MM-YYYY HH:mm:ss', // 📌 Formato en "día-mes-año"
                    'separator' => ' - ',
                    'applyLabel' => 'Aplicar',
                    'cancelLabel' => 'Cancelar',
                    'fromLabel' => 'Desde',
                    'toLabel' => 'Hasta',
                    'customRangeLabel' => 'Personalizado',
                    'weekLabel' => 'S',
                    'daysOfWeek' => ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                    'monthNames' => ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                    'firstDay' => 1 // 📅 La semana comienza en lunes
                ],
                'cancelButtonClasses' => 'btn-danger',
                'opens' => 'center',
            ];
        @endphp-->

        <div class="col-md-6 col-lg-6">
            <x-adminlte-date-range name="date_range" label="Rango de fecha/hora" :config="$config">
                <x-slot name="prependSlot">
                    <div class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </div>
                </x-slot>

            </x-adminlte-date-range>
        </div>


        <div class="col-md-6 col-lg-6">
            <div class="form-group">
                <label>Ministerio:</label>
                <x-adminlte-select2 name="ministerio_id" class="form-control">
                    <option value="" selected disabled>Seleccione un ministerio</option>
                    @foreach ($ministerios as $ministerio)
                        <option value="{{ $ministerio->id }}" {{ old('ministerio_id', 3) == $ministerio->id ? 'selected' : '' }}>
                            {{ $ministerio->dept_name }}
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