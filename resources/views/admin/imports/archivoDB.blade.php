@extends('adminlte::page')

@section('title', $pageTitle)

@section('content')
    <div class="card card-success shadow">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-database"></i> Importar datos de Asistencia</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Minimizar">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="card-body">
            <!-- Formulario Dropzone -->
            <form action="{{ route('admin.imports.archivoDB') }}" method="POST"
                class="dropzone rounded border p-3 bg-light" id="archivoDB-dropzone" enctype="multipart/form-data">
                @csrf
            </form>

            <!-- Información de la última importación -->
            <div class="mt-4">
                @if ($ultimaImportacion)
                    <div class="card card-info shadow">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-info-circle"></i> Información de la Última Importación
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Minimizar">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-unbordered">
                                <li class="list-group-item">
                                    <strong>Archivo:</strong>
                                    <span class="text-primary">{{ $ultimaImportacion->archivo }}</span>
                                </li>
                                <li class="list-group-item">
                                    <strong>Fecha:</strong>
                                    <span
                                        class="text-secondary">{{ $ultimaImportacion->created_at->format('d/m/Y H:i:s') }}</span>
                                </li>
                                <li class="list-group-item">
                                    <strong>Usuario:</strong>
                                    <span class="text-success">
                                        {{ $ultimaImportacion->usuario->name ?? 'No disponible' }}
                                        {{ $ultimaImportacion->usuario->last_name ?? 'No disponible' }}
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <strong>Estado:</strong>
                                    <span
                                        class="badge badge-{{ $ultimaImportacion->estado == 'procesado' ? 'success' : ($ultimaImportacion->estado == 'pendiente' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($ultimaImportacion->estado) }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                @else
                    <div class="card card-secondary shadow">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-exclamation-circle"></i> Sin Importaciones</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">No se ha realizado ninguna importación hasta el momento.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-12">
            <div class="callout callout-warning">
                <p><i class="fas fa-clock"></i> La última importación se registra automáticamente después de subir un nuevo
                    archivo</p>
            </div>
        </div>
    </div>
@stop

@push('css')
@endpush

@push('js')
    <script>
        Dropzone.options.archivoDBDropzone = {
            paramName: "archivo", // Nombre del archivo enviado al servidor
            maxFilesize: 10, // Tamaño máximo permitido (en MB)
            acceptedFiles: ".db", // Solo se aceptan archivos .db
            dictDefaultMessage: "Arrastra tu archivo aquí o haz clic para seleccionar",
            autoProcessQueue: true, // Habilita la subida automática
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}" // Token CSRF para la seguridad
            },
            init: function() {
                this.on("success", function(file, response) {
                    let mensaje = "¡Archivo importado con éxito!";

                    if (response.nuevas_asistencias > 0) {
                        mensaje =
                            `Se registraron ${response.nuevas_asistencias} nuevas asistencias.`;
                    } else {
                        mensaje = "No se encontraron nuevas asistencias.";
                    }

                    Swal.fire({
                        title: "¡Éxito!",
                        text: mensaje,
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        location.reload(); 
                    });
                });

                this.on("error", function(file, response) {
                    let mensajeError = "Hubo un error al subir el archivo.";

                    if (typeof response === 'object' && response.error) {
                        mensajeError = "Error del servidor: " + response.error;
                    } else if (typeof response === 'string') {
                        mensajeError = "Error del servidor: " + response;
                    }

                    Swal.fire({
                        title: "Error",
                        text: mensajeError,
                        icon: "error",
                        confirmButtonText: "Cerrar"
                    });
                });
            }
        };
    </script>
@endpush
