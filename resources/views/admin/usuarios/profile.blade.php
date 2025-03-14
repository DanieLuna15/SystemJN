@extends('adminlte::page')
@section('title', $pageTitle)

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <!-- Imagen de perfil -->
                    <label for="profile_image_input" class="d-block text-center">
                        <img id="profileImagePreview" class="profile-user-img img-fluid img-circle mx-auto d-block"
                            src="{{ asset($user->profile_image ?? 'images/default-dark.png') }}"
                            style="width: 180px; height: 180px; object-fit: cover; cursor: pointer;">
                    </label>
                    <input type="file" name="profile_image" id="profile_image_input" style="display: none;"
                        accept="image/*" onchange="document.getElementById('profileImagePreview').src = window.URL.createObjectURL(this.files[0])">

                    <!-- Datos del usuario -->

                    <h3 class="text-center">{{ $user->name }} {{ $user->last_name }}</h3>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Cargo</b>
                            <a class="float-right">.........</a>
                        </li>
                        <li class="list-group-item">
                            <b>Ministerio</b>
                            <a class="float-right">............</a>
                        </li>
                    </ul>

                </div>
            </div>

            <div class="card card-primary mt-3">
                <div class="card-header">
                    <h3 class="card-title">Sobre mi</h3>
                </div>
                <div class="card-body">
                    <strong><i class="fas-regular fa-mobile-retro"></i> Teléfono</strong>
                    <p class="text-muted">{{ $user->phone }}</p>
                    <hr>
                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Ubicación</strong>
                    <p class="text-muted">{{ $user->address }}</p>
                    <hr>
                    <strong><i class="fas fa-pencil-alt mr-1"></i> Email</strong>
                    <p class="text-muted">{{ $user->email }}</p>
                    <hr>
                    <strong><i class="far fa-file-alt mr-1"></i> Carnet de Identidad</strong>
                    <p class="text-muted">{{ $user->ci }}</p>
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
                            <a class="nav-link" href="#perfil" data-toggle="tab">Perfil</a>
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
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Nombre:</label>
                                        <input id="name" name="name" value="{{ old('name', $user->name) }}"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name">Apellido:</label>
                                        <input id="last_name" name="last_name"
                                            value="{{ old('last_name', $user->last_name) }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ci">CI:</label>
                                        <input id="ci" name="ci" value="{{ old('ci', $user->ci) }}"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Correo:</label>
                                        <input id="email" name="email" value="{{ old('email', $user->email) }}"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Teléfono</Table>:</label>
                                        <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Perfil -->
                        <div class="tab-pane fade" id="perfil">
                            <h5 class="text-center">Perfil</h5>
                            <p>perfil.</p>
                        </div>

                        <!-- Seguridad y Privacidad -->
                        <div class="tab-pane fade" id="seguridad">
                            <form>
                                <label for="password_actual">Contraseña Actual</label>
                                <div class="input-group">
                                    <input type="password" id="password_actual" name="password_actual" class="form-control"
                                        placeholder="Ingrese contraseña actual" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" id="toggleButton_actual"
                                            onclick="togglePassword('password_actual', 'eyeIcon_actual')">
                                            <i id="eyeIcon_actual" class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <label for="password">Nueva Contraseña</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control"
                                        placeholder="Ingrese su nueva contraseña" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" id="toggleButton_new"
                                            onclick="togglePassword('password', 'eyeIcon_new')">
                                            <i id="eyeIcon_new" class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <label for="password_confirmation">Confirmar Nueva Contraseña</label>
                                <div class="input-group">
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        class="form-control" placeholder="Ingrese contraseña actual" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" id="toggleButton_confirmation"
                                            onclick="togglePassword('password_confirmation', 'eyeIcon_confirmation')">
                                            <i id="eyeIcon_confirmation" class="fa fa-eye"></i>
                                        </button>
                                    </div>

                                </div>
                            </form>

                            <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1cm;">Actualizar
                                Contraseña</button>
                        </div>

                        <script>
                            function togglePassword(inputId, iconId) {
                                let passwordInput = document.getElementById(inputId);
                                let eyeIcon = document.getElementById(iconId);
                                let toggleButton = document.getElementById('toggleButton_' + inputId);

                                // cambio para el ojito
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

                    </div>
                </div>
            </div>
        </div>
    @stop


    @push('css')
    @endpush
    {{-- <form action="{{ route('profile.updatePassword') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="password_actual">Contraseña Actual</label>
                                <input type="password" name="password_actual" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="password_nueva">Nueva Contraseña</label>
                                <input type="password" name="password_nueva" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="password_confirm">Confirmar Nueva Contraseña</label>
                                <input type="password" name="password_confirm" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Actualizar Contraseña</button>
                        </form> --}}




    {{-- {{-- <div class="tab-pane" id="contacto"> --}}
    <!-- Sección de información de contacto -->
    {{-- <h5 class="text-center">Información de Contacto</h5>
<p>Teléfono: {{ $user->phone }}</p>
<p>Dirección: {{ $user->address }}</p>
<p>Ciudad: {{ $user->city }}</p>
</div> --}}

    {{-- <div class="tab-pane" id="seguridad">
                                            <!-- Sección de seguridad y privacidad -->
                                            <h5 class="text-center">Seguridad y Privacidad</h5>
                                            <p>Contraseña: ********</p>
                                            <p>Último inicio de sesión: {{ $user->last_login }}</p>
                                            <p>Estado de cuenta: {{ $user->status }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="general"> --}}
    {{-- <h5 class="text-center">Información General</h5> --}}
    <!-- Sección de información general del perfil -->
    {{-- <div class="tab-pane fade show active" id="general">
                                                <h5 class="text-center">Información General</h5>

                                            </div>
                                        </div>

                                        <!-- Sección de contraseña -->
                                        <div class="tab-pane fade" id="contraseña">
                                            <h5 class="text-center">Contraseña</h5>
                                            <div class="row">
                                                <div class="col-md-12 col-lg-12">
                                                    <div class="form-group">
                                                        <label for="password">Contraseña actual:</label>
                                                        <div class="input-group">
                                                            <input id="password" name="password" type="password"
                                                                class="form-control">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12 col-lg-12">
                                                    <div class="form-group">
                                                        <label for="new_password">Nueva contraseña:</label>
                                                        <div class="input-group">
                                                            <input id="new_password" name="new_password" type="password"
                                                                class="form-control">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12 col-lg-12">
                                                    <div class="form-group">
                                                        <label for="confirm_password">Confirmar contraseña:</label>
                                                        <div class="input-group">
                                                            <input id="confirm_password" name="confirm_password"
                                                                type="password" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> --}}

    {{-- <!-- Columna de Foto de Perfil -->
                                        <div class="col-md-4">
                                            <div class="card card-primary card-outline">
                                                <div class="card-header p-2">
                                                    <h5 class="text-center">Foto de Perfil</h5>
                                                </div>
                                                <div class="card-body text-center">
                                                    <label for="profile_image_input">
                                                        <img id="profileImagePreview"
                                                            class="profile-user-img img-fluid img-circle"
                                                            src="{{ asset($user->profile_image ?? 'images/default-dark.png') }}"
                                                            alt="Imagen de perfil"
                                                            style="width: 180px; height: 180px; object-fit: cover; cursor: pointer;">
                                                    </label>
                                                    <input type="file" name="profile_image" id="profile_image_input"
                                                        style="display: none;" accept="image/*"
                                                        onchange="document.getElementById('profileImagePreview').src = window.URL.createObjectURL(this.files[0])">
                                                </div>
                                            </div>
                                        </div> --}}
