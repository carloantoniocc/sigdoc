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

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body class= "background-login">
    <div id="app2">
        <nav class="navbar navbar-default navbar-static-top background-menu">
            <div class="container-fluid">
                <div class="navbar-header">
                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <img alt="" src="{{ asset('image/logo.png') }}">
                    </a>
                </div>

            </div>
        </nav>
		
		<!--Div visible solo en pantallas anchas-->
		<div class="col-sm-12 visible-lg"></br></br></div>
		
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
