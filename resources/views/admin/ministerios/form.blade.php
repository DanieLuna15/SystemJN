<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.ministerios.save', $ministerio->id ?? null) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{ $ministerio->id ?? '' }}">

            <div class="form-group">
                <label>Nombre:</label>
                <input type="text" name="nombre" class="form-control"
                    value="{{ old('nombre', $ministerio->nombre ?? '') }}">
                @error('nombre')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Imagen:</label>
                <input type="file" name="logo" class="form-control">
                @if (!empty($ministerio->logo))
                    <br>
                    <img src="{{ asset($ministerio->logo) }}" alt="Logo" width="100">
                @endif
                @error('logo')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Multa por Retraso (Bs):</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <b>Bs.</b>
                        </span>
                    </div>
                    <input type="number" name="multa_incremento" class="form-control" step="0.01"
                        value="{{ old('multa_incremento', $ministerio->multa_incremento ?? '') }}">
                </div>
                @error('multa_incremento')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-success">
                {{ isset($ministerio->id) ? 'Actualizar' : 'Guardar' }}
            </button>

            <a href="{{ route('admin.ministerios.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
