@extends('adminlte::page')
@section('title', $pageTitle)

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile text-center">
                    <!-- Imagen de perfil -->
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle"
                            src="{{ asset($usuario->profile_image ?? 'images/default-dark.png') }}" alt="Foto de perfil">
                    </div>
                    <!-- Datos del usuario -->
                    <ul class="list-group list-group-unbordered mb-3">
                        <!-- Nombre completo -->
                        <li class="list-group-item text-center">
                            <b><i class="fas fa-user"></i> {{ $usuario->name . ' ' . $usuario->last_name }}</b>
                        </li>
                        <!-- Rol del usuario -->
                        <li class="list-group-item text-center">
                            <b><i class="fas fa-user-shield"></i> Rol:</b>
                            <p>
                                @if ($usuario->roles->isNotEmpty())
                                    @foreach ($usuario->roles as $role)
                                        @if ($role->id == 1)
                                            <span class="badge bg-gradient-primary">
                                                <i class="fas fa-crown"></i> {{ ucfirst($role->name) }}
                                            </span>
                                        @elseif ($role->id == 2)
                                            <span class="badge bg-gradient-success">
                                                <i class="fas fa-star"></i> {{ ucfirst($role->name) }}
                                            </span>
                                        @else
                                            <span class="badge bg-gradient-info">
                                                <i class="fas fa-user-tie"></i> {{ ucfirst($role->name) }}
                                            </span>
                                        @endif
                                    @endforeach
                                @else
                                    <span class="badge bg-gradient-secondary">
                                        <i class="fas fa-minus"></i> Sin rol asignado
                                    </span>
                                @endif
                            </p>
                        </li>
                        <!-- Ministerios del usuario -->
                        <li class="list-group-item text-center">
                            <b><i class="fas fa-church"></i> Ministerios:</b>
                            <p>
                                @if ($usuario->ministerios->isNotEmpty())
                                    @foreach ($usuario->ministerios as $ministerio)
                                        <span class="badge badge-info">
                                            <i class="fas fa-users"></i> {{ $ministerio->nombre }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-minus"></i> Sin ministerios asignados
                                    </span>
                                @endif
                            </p>
                        </li>
                        <li class="list-group-item text-center">
                            <b><i class="fas fa-church"></i> Ministerios Liderados:</b>
                            <p>
                                @if ($usuario->ministeriosLiderados->isNotEmpty())
                                    @foreach ($usuario->ministeriosLiderados as $ministerio)
                                        <span class="badge badge-info">
                                            <i class="fas fa-users"></i> {{ $ministerio->nombre }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-minus"></i> Sin ministerios liderados
                                    </span>
                                @endif
                            </p>
                        </li>
                    </ul>
                </div>
            </div>
            <!--SOBRE MI-->

            <div class="card card-primary mt-3">
                <div class="card-header">
                    <h3 class="card-title">Sobre mi</h3>
                </div>
                <div class="card-body">
                    <div>
                        @foreach ([
            'phone' => ['icon' => 'fas fa-phone', 'label' => 'Teléfono'],
            'address' => ['icon' => 'fas fa-map-marker-alt mr-1', 'label' => 'Dirección'],
            'email' => ['icon' => 'fas fa-pencil-alt mr-1', 'label' => 'Email'],
            'ci' => ['icon' => 'far fa-file-alt mr-1', 'label' => 'Carnet de Identidad'],
        ] as $field => $data)
                            <strong><i class="{{ $data['icon'] }}"></i> {{ $data['label'] }}</strong>
                            <p class="text-muted">{{ $usuario->$field ?? 'No disponible' }}</p>
                            <hr>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card card-primary card-outline">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active" href="#general" data-toggle="tab">Información General</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#perfil" data-toggle="tab">Foto de Perfil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#seguridad" data-toggle="tab">Seguridad y Privacidad</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Información General -->
                        <div class="tab-pane fade show active" id="general">

                            <!-- Campo Datos Personales -->
                            <form action="{{ route('admin.usuarios.update', $usuario->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="form_type" value="secundario">
                                <div class="row">
                                    @foreach ([
            'name' => 'Nombre',
            'last_name' => 'Apellido',
            'ci' => 'CI',
            'email' => 'Email',
            'phone' => 'Teléfono',
        ] as $field => $label)
                                        <div class="col-md-6">
                                            <x-adminlte-input name="{{ $field }}" label="{{ $label }}:"
                                                value="{{ old($field, $usuario->$field ?? 'No disponible') }}" />
                                        </div>
                                    @endforeach

                                    <!-- Dirección al final -->
                                    <div class="col-md-12">
                                        <x-adminlte-textarea name="address" label="Dirección:" rows=3>
                                            {{ old('address', $usuario->address ?? 'No disponible') }}
                                        </x-adminlte-textarea>
                                    </div>
                                </div>


                                <div class="d-flex justify-content-between mt-3">
                                    <x-adminlte-button class="btn w-100" type="submit"
                                        label="{{ isset($usuario->id) ? 'Guardar cambios' : 'Guardar' }}" theme="success"
                                        icon="fas fa-lg fa-save" />
                                </div>
                            </form>
                        </div>
                        <!-- Foto de Perfil -->
                        <div class="tab-pane fade" id="perfil">
                            <form action="{{ route('admin.usuarios.updateImage', $usuario->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT') <!-- Corregido: para que acepte el método PUT -->

                                <x-image-upload label="Imagen de perfil" name="profile_image"
                                    alt="Imagen de perfil del usuario" :image="$usuario->profile_image ?? null" :id="'imagen_perfil'" />
                                <!-- Cambié a "profile_image" -->

                                <input type="hidden" name="remove_imagen" id="removeImagenInput_imagen_perfil"
                                    value="0">

                                <div class="d-flex justify-content-between mt-3">
                                    <x-adminlte-button class="btn w-100" type="submit" label="Guardar imagen"
                                        theme="success" icon="fas fa-lg fa-save" />
                                </div>
                            </form>
                        </div>
                        <!-- Seguridad y Privacidad -->
                        <div class="tab-pane fade" id="seguridad">
                            <form method="POST" action="{{ route('admin.usuarios.updatePassword', auth()->id()) }}">
                                @csrf
                                @method('PUT')

                                <label for="password_actual">Contraseña Actual</label>
                                <div class="input-group">
                                    <input type="password" id="password_actual" name="password_actual"
                                        class="form-control" placeholder="Ingrese contraseña actual" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button"
                                            onclick="togglePassword('password_actual', 'eyeIcon_actual')">
                                            <i id="eyeIcon_actual" class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('password_actual')
                                    <p style="color: red;">{{ $message }}</p>
                                @enderror

                                <label for="password">Nueva Contraseña</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control"
                                        placeholder="Ingrese su nueva contraseña" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button"
                                            onclick="togglePassword('password', 'eyeIcon_new')">
                                            <i id="eyeIcon_new" class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('password')
                                    <p style="color: red;">{{ $message }}</p>
                                @enderror

                                <label for="password_confirmation">Confirmar Nueva Contraseña</label>
                                <div class="input-group">
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        class="form-control" placeholder="Confirme su nueva contraseña" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button"
                                            onclick="togglePassword('password_confirmation', 'eyeIcon_confirmation')">
                                            <i id="eyeIcon_confirmation" class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('password_confirmation')
                                    <p style="color: red;">{{ $message }}</p>
                                @enderror

                                <button type="submit" class="btn btn-primary btn-block"
                                    style="margin-top: 1cm;">Actualizar Contraseña</button>
                            </form>


                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>
@stop
@push('js')
    <script>
        function togglePassword(inputId, iconId) {
            let passwordInput = document.getElementById(inputId);
            let eyeIcon = document.getElementById(iconId);
            let toggleButton = document.getElementById('toggleButton_' + inputId);

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye");
            }
        }
    </script>
@endpush
