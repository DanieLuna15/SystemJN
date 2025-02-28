<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customError.css') }}">
    @yield('css') <!-- Incluye esta línea para los estilos específicos de la vista -->
</head>
<body class="hold-transition sidebar-mini @yield('body-class')">
    <div class="wrapper d-flex align-items-center justify-content-center"> <!-- Ajuste de altura para dejar espacio al footer -->
        <!-- Contenido principal -->
        @yield('content')
    </div>

    <footer style="position: fixed; bottom: 0; width: 100%; text-align: center; color: #fff; padding: 1rem 0; background-color: inherit;">
        JN © 2025 Todos los derechos reservados.
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    @yield('script') <!-- Incluye esta línea para los scripts específicos de la vista -->
</body>
</html>
