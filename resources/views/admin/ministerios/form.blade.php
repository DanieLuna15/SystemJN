<div class="card">
    <div class="card-body">
        <form action="{{ $ministerio->id ? route('admin.ministerios.update', $ministerio) : route('admin.ministerios.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if ($ministerio->id)
                @method('PUT')
            @endif

            <div class="form-group">
                <label>Nombre:</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $ministerio->nombre) }}">
                @error('nombre')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label>Logo:</label>
                <input type="file" name="logo" class="form-control">
                @if ($ministerio->logo)
                    <br>
                    <img src="{{ asset($ministerio->logo) }}" alt="Logo" width="100">
                @endif
                @error('logo')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label>Multa por Retraso (Bs):</label>
                <input type="number" name="multa_incremento" class="form-control" step="0.01" value="{{ old('multa_incremento', $ministerio->multa_incremento) }}" >
                @error('multa_incremento')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label>Hora de Tolerancia:</label>
                <input type="time" name="hora_tolerancia" class="form-control" value="{{ old('hora_tolerancia', $ministerio->hora_tolerancia) }}" >
                @error('hora_tolerancia')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-success">Guardar</button>
            <a href="{{ route('admin.ministerios.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
