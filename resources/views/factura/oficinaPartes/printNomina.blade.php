<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<!-- CSRF Token -->
		<meta name="csrf-token" content="{{ csrf_token() }}">
		
		<title>{{ config('app.name', 'Laravel') }}</title>
		
		<!-- Estilos -->
		<style>
		.container {
			position: relative;
		}
		.title {
			margin-left: 400px;
			font-family: Arial, Helvetica, sans-serif;
			text-align: left;
			padding-top: 30px;
			font-size: 14px;
		}
		.body {
			position: relative;
			margin-top: 5px;
			width: 100%;
		}
		.titulo {
			position: relative;
			font-family: Arial, Helvetica, sans-serif;
			font-weight: bold;
			font-size: 12px;
			padding: 5px;
		}
		.detalle {
			position: relative;
			font-family: Arial, Helvetica, sans-serif;
			font-size: 12px;
			padding: 5px;
		}
		.left {
			position: absolute;
			margin-left: 30px;
		}
		.right {
			position: relative;
			margin-left: 70px;
		}
		table {
			border-collapse: collapse;
			width: 100%;
		}

		table, td, th {
			border: 1px solid black;
			text-align:center;
		}
		
		.code {
			position: relative;
			margin-left:315px
		}
		</style>
	</head>
	<body>
		<div class="container">
			<div class="body">
				<div class="titulo">
					<h2>Oficina de Partes / Consulta Nomina</h2>
					
				</div>
				<br>
				<div class="detalle">
					<table>
						<tr>
							<td>Nómina</td>
							<td>RUT</td>
							<td>Proveedor</td>
							<td>Tipo Documento</td>
							<td>Nro Documento</td>
							<td>Emisión</td>
							<td>Monto</td>
							<td>Recepción</td>
						</tr>
						@foreach($documentos as $documento)
						<tr>
							<td>{{$documento->nomina}}</td>
							<td>{{$documento->rut}}</td>
							<td>{{$documento->nameProveedor}}</td>
							<td>{{$documento->tipoDoc}}</td>
							<td>{{$documento->nDoc}}</td>
							<td>{{$documento->fechaEmision}}</td>
							<td>{{$documento->monto}}</td>
							<td>{{$documento->fechaRecepcion}}</td>
						</tr>
						@endforeach	
					</table>
				</div>
			</div>
		</div>
	</body>
</html>

