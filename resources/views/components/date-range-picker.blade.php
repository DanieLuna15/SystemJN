@props(['name', 'label', 'config'])

@php
    // Obtener el rango de fechas desde la sesión o utilizar los valores predeterminados
    $dateRange = session('date_range', now()->startOfMonth()->format('d-m-Y 00:00:00') . ' - ' . now()->endOfMonth()->format('d-m-Y 23:59:59'));
@endphp

<div class="form-group">
    <label>{{ $label }}</label>
    <x-adminlte-date-range :name="$name" :config="$config" :value="$dateRange">
        <x-slot name="prependSlot">
            <div class="input-group-text">
                <i class="far fa-calendar-alt"></i>
            </div>
        </x-slot>
    </x-adminlte-date-range>
</div>
