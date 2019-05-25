<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
	
	<!-- favicon -->
	<link rel="shortcut icon" href="{{ asset('image/favicon.ico') }}" type="image/x-icon">
	<link rel="icon" href="{{ asset('image/favicon.ico') }}" type="image/x-icon">
	
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('css/sigdoc.css') }}" rel="stylesheet">
	<link href="{{ asset('css/jquery-ui.min.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>
	<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
	<script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
    <div id="app1">
        <nav class="navbar navbar-default navbar-static-top background-menu">
            <div class="container-fluid">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar" style="background-color:#ffffff"></span>
                        <span class="icon-bar" style="background-color:#ffffff"></span>
                        <span class="icon-bar" style="background-color:#ffffff"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <img alt="" src="{{ asset('image/logo.png') }}">
                    </a>
                </div>
				
				<!-- Incluye Menú -->
				@include('layouts.menu')
            </div>
        </nav>

        @yield('content')
    </div>
    <script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>
    <!-- Scripts -->
    <!-- SCRIPT DESHABILITA BOTON -->
    <script>
        $("form").submit(function(){
            $('#send').prop('disabled', true);
        });    
    </script>
    <!-- FIN SCRIPT DESHABILITA BOTON -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
