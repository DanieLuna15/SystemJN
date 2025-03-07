<x-adminlte-card>
    <form action="{{ route('admin.ministerios.save', $ministerio->id ?? null) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{ $ministerio->id ?? '' }}">
        <div class="row">
            <div class="col-lg-4">
                <div class="col-sm-12">
                    <!-- Campo Imagen -->
                    <x-image-upload label="Imagen" name="logo" :image="$ministerio->logo ?? null" :id="'logotipo_actual'" />
                    <input type="hidden" name="remove_logo" id="removeLogoInput_logotipo_actual" value="0">
                </div>
            </div>
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <!-- Campo Nombre -->
                        <x-adminlte-input name="nombre" label="Nombre:"
                            value="{{ old('nombre', $ministerio->nombre ?? '') }}" />
                    </div>

                    <div class="col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Categoría:</label>
                            <x-adminlte-select2 name="tipo" class="form-control">
                                <option value="" disabled>Seleccione Categoría</option>
                                @foreach ([1 => 'Alto', 0 => 'Estandar'] as $key => $type)
                                    <option value="{{ $key }}"
                                        {{ old('tipo', $ministerio->tipo ?? 0) == $key ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </x-adminlte-select2>
                        </div>
                    </div>

                    <div class="col-md-12 col-lg-12">
                        <!-- Campo Multa por Retraso -->
                        <x-adminlte-input name="multa_incremento" label="Sancion por Retraso (Bs):" type="number"
                            step="0.01" value="{{ old('multa_incremento', $ministerio->multa_incremento ?? '') }}">
                            <x-slot name="prependSlot">
                                <div class="input-group-text">
                                    <b>Bs.</b>
                                </div>
                            </x-slot>
                            <x-slot name="bottomSlot">
                                <span class="text-sm text-gray">
                                    [Este es el monto de multa acumulativa.]
                                </span>
                            </x-slot>
                        </x-adminlte-input>
                    </div>


                    {{-- With multiple slots, and plugin config parameters --}}
                    @php
                        $config = [
                            'placeholder' => 'Select multiple options...',
                            'allowClear' => true,
                        ];
                    @endphp
                    <div class="col-md-12 col-lg-12">
                        <x-adminlte-select2 id="sel2Category" name="sel2Category[]" label="Categories"
                            label-class="text-danger" igroup-size="sm" :config="$config" multiple>
                            <x-slot name="prependSlot">
                                <div class="input-group-text bg-gradient-red">
                                    <i class="fas fa-tag"></i>
                                </div>
                            </x-slot>
                            <x-slot name="appendSlot">
                                <x-adminlte-button theme="outline-dark" label="Clear"
                                    icon="fas fa-lg fa-ban text-danger" />
                            </x-slot>
                            <option>Sports</option>
                            <option>News</option>
                            <option>Games</option>
                            <option>Science</option>
                            <option>Maths</option>
                        </x-adminlte-select2>
                    </div>
                </div>

            </div>
        </div>
        <!-- Botones de Acción -->
        <div class="d-flex justify-content-between">
            <x-adminlte-button class="btn w-100" type="submit"
                label="{{ isset($ministerio->id) ? 'Guardar cambios' : 'Guardar' }}" theme="success"
                icon="fas fa-lg fa-save" />
        </div>
    </form>
</x-adminlte-card>

@push('breadcrumb-plugins')
    <a href="{{ route('admin.ministerios.index') }}" class="btn btn-secondary rounded">
        <i class="fas fa-undo"></i> Regresar
    </a>
@endpush
