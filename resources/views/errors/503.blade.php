<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
	
	<!-- favicon -->
	<link rel="shortcut icon" href="{{ asset('image/favicon.ico') }}" type="image/x-icon">
	<link rel="icon" href="{{ asset('image/favicon.ico') }}" type="image/x-icon">
	
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('css/sigdoc.css') }}?v=1.0" rel="stylesheet">
	
	<style>
		.full-height {
			height: 40vh;
		}

		.flex-center {
			align-items: center;
			display: flex;
			justify-content: center;
		}

		.position-ref {
			position: relative;
		}

		.title {
			font-size: 64px;
			font-family: 'Raleway', sans-serif;
			font-weight: 80;
			color: #636b6f;
			text-align: center;
		}
	</style>
	
    <!-- Scripts -->
    <script>
        window.Laravel = {"csrfToken":null};
    </script>
</head>
<body>
    <div id="app2">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<div class="logodti">	
						<img alt="" src="{{ asset('image/logoDTI.png') }}">
					</div>
					<br><br>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="flex-center position-ref full-height">
						<div class="title">
							<p>SIGDOC se encuentra en Mantención</p> 
							<p>Estaremos de vuelta pronto.</p>
						</div>
					</div>
				</div>
			</div>
		</div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
	<script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>
	<script src="{{ asset('js/establecimiento.js') }}"></script>
</body>
</html>