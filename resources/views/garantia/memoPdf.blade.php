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
		.head {
			position: relative;
			height: 140px;
			width: 100%;
		}
		.image {
			position: absolute;
			left: 0px;
			width: 200px;
			font-family: Arial, Helvetica, sans-serif;
			font-size: 10px;
		}
		.image>img {
			height: 100px;
			width: auto;
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
			margin-left: 30px;
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
			<div class="head">
				<div class="image">
					<img src="http://10.8.64.44/sic/image/SSMOC.jpg">
					<br>
					Departamento de Gestión de Convenios
					<br>
					{{mb_strtoupper($user1->iniciales)}}
				</div>
				<div class="title">
					<b>MEMORANDUM N° {{$garantia->id}}</b>
					<br><br>
					<b>SANTIAGO</b>, {{$fecha}}
				</div>
			</div>
			</br></br>
			<div class="body">
				<div class="titulo">
					<div class="left">DE:</div>
					<div class="right">{{mb_strtoupper($user1->name)}}</div>
					<div class="left"></div>
					<div class="right">JEFE(A) DEPARTAMENTO DE GESTIÓN DE CONVENIOS</div>
				</div>
				<div class="titulo">
					<div class="left">A:</div>
					<div class="right">{{mb_strtoupper($user2->name)}}</div>
					<div class="left"></div>
					<div class="right">JEFE(A) DEPARTAMENTO GESTIÓN FINANCIERA</div>
				</div>
				<br>
				<div class="detalle">
					Junto con saludar, solicito a usted gestionar la devolución de la siguiente Boleta de {{ $objeto->name }}: 
				</div>
				<br>
				<div class="detalle">
					<table>
						<tr>
							<td>PROVEEDOR</td>
							<td>RUT</td>
							<td>N° GARANTIA</td>
							<td>MONTO GARANTIA</td>
							<td>MOTIVO DE DEVOLUCIÓN</td>
						</tr>
						<tr>
							<td>{{$proveedor->name}}</td>
							<td>{{$proveedor->rut}}-{{$proveedor->dv}}</td>
							<td>{{$garantia->nDoc}}</td>
							<td>{{$garantia->monto}} {{$moneda->name}}</td>
							<td>{{$movimiento->observacion}}</td>
						</tr>
					</table>
				</div>
				<br>
				<div class="detalle">
					Saluda atentamente a usted,
				</div>
				<br>
				<div class="code">
					{!! DNS2D::getBarcodeHTML($user1->name, "QRCODE",4,4) !!}
				</div>	
				<br>
				<div class="titulo" style="text-align:center">
					{{mb_strtoupper($user1->name)}}
					<br>
					JEFE(A) DEPARTAMENTO DE GESTIÓN DE CONVENIOS
				</div>
			</div>
		</div>
	</body>
</html>

