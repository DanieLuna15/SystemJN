@props(['name', 'label', 'config'])

<div class="form-group">
    <label>{{ $label }}</label>
    <x-adminlte-date-range :name="$name" :config="$config">
        <x-slot name="prependSlot">
            <div class="input-group-text">
                <i class="far fa-calendar-alt"></i>
            </div>
        </x-slot>
    </x-adminlte-date-range>
</div>
