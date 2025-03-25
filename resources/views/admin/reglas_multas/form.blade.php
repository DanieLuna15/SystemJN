@php
    $select2Config = config('select2');
@endphp

<x-adminlte-card>
    <form action="{{ route('admin.reglas_multas.save', $regla_multa->id ?? null) }}" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{ $regla_multa->id ?? '' }}">
        <div class="row">


            <!-- Ministerios -->
            <div class="col-md-12 col-lg-12">
                <x-adminlte-select2 id="ministeriosSelect" name="ministerio_id[]" label="Ministerios"
                    :config="array_merge($select2Config, ['placeholder' => 'Seleccione uno o varios ministerios para asociar con esta regla de multa...'])"
                    multiple>
                    @foreach ($ministerios as $ministerio)
                        <option value="{{ $ministerio->id }}" {{ in_array($ministerio->id, old('ministerio_id', $regla_multa->ministerios->pluck('id')->toArray() ?? [])) ? 'selected' : '' }}>
                            {{ $ministerio->nombre }}
                        </option>
                    @endforeach
                </x-adminlte-select2>
                
            </div>

            <div class="col-md-12 col-lg-12">
                <!-- Campo Descripcion -->
                <x-adminlte-textarea name="descripcion" label="Descripción:" fgroup-class="col-md-12" rows=3>
                    {{ old('descripcion', $regla_multa->descripcion ?? '') }}
                </x-adminlte-textarea>
            </div>

            <div class="col-md-4 col-lg-4">
                <!-- Campo Multa por falta -->
                <x-adminlte-input name="multa_por_falta" label="Multa por falta:" type="number" step="0.01"
                    value="{{ old('multa_por_falta', $regla_multa->multa_por_falta ?? '') }}">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <b>Bs.</b>
                        </div>
                    </x-slot>
                    <x-slot name="bottomSlot">
                        <span class="text-sm text-gray">
                            [Este es el monto de multa por Falta.]
                        </span>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <div class="col-md-4 col-lg-4">
                <!-- Campo Multa incremental -->
                <x-adminlte-input name="multa_incremental" label="Multa incremental:" type="number" step="0.01"
                    value="{{ old('multa_incremental', $regla_multa->multa_incremental ?? '') }}">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <b>Bs.</b>
                        </div>
                    </x-slot>
                    <x-slot name="bottomSlot">
                        <span class="text-sm text-gray">
                            [Este es el monto de multa incremental.]
                        </span>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <div class="col-md-4 col-lg-4">
                <!-- Campo Multa por retraso largo -->
                <x-adminlte-input name="multa_por_retraso_largo" label="Multa por retraso largo:" type="number" step="0.01"
                    value="{{ old('multa_por_retraso_largo', $regla_multa->multa_por_retraso_largo ?? '') }}">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <b>Bs.</b>
                        </div>
                    </x-slot>
                    <x-slot name="bottomSlot">
                        <span class="text-sm text-gray">
                            [Este es el monto de multa por retraso largo.]
                        </span>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <div class="col-md-4 col-lg-4">
                <!-- Campo Minutos por incremento -->
                <x-adminlte-input name="minutos_por_incremento" label="Minutos por incremento:" type="number" step="1" min="0"
                    value="{{ old('minutos_por_incremento', $regla_multa->minutos_por_incremento ?? '') }}">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                    </x-slot>
                    <x-slot name="bottomSlot">
                        <span class="text-sm text-gray">
                            [Define los minutos que representan un incremento.]
                        </span>
                    </x-slot>
                </x-adminlte-input>
            </div>

            <div class="col-md-4 col-lg-4">
                <!-- Campo Minutos de retraso largo -->
                <x-adminlte-input name="minutos_retraso_largo" label="Minutos de retraso largo:" type="number" step="1" min="0"
                    value="{{ old('minutos_retraso_largo', $regla_multa->minutos_retraso_largo ?? '') }}">
                    
                    <!-- Ícono al inicio del input -->
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-clock"></i> <!-- Icono relacionado al tiempo -->
                        </div>
                    </x-slot>
                    
                    <!-- Texto adicional debajo del input -->
                    <x-slot name="bottomSlot">
                        <span class="text-sm text-gray">
                            [Define el tiempo en minutos que representa un retraso largo.]
                        </span>
                    </x-slot>
                </x-adminlte-input>
            </div>
            

        </div>

        <!-- Botones de Acción -->
        <div class="d-flex justify-content-between">
            <x-adminlte-button class="btn w-100" type="submit"
                label="{{ isset($regla_multa->id) ? 'Guardar cambios' : 'Guardar' }}" theme="success"
                icon="fas fa-lg fa-save" />
        </div>
    </form>
</x-adminlte-card>

@push('breadcrumb-plugins')
    <a href="{{ route('admin.reglas_multas.index') }}" class="btn btn-secondary rounded">
        <i class="fas fa-undo"></i> Regresar
    </a>
@endpush