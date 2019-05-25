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
					<img src="http://10.8.64.29/sigdoc/image/SSMOC.jpg">
					<br> 
					Departamento de {{$referente->name}}
					<br>
					@if($user != null)
						{{mb_strtoupper($user->iniciales)}}
					@endif	
				</div>
				<div class="title">
					<b>MEMORANDUM N° {{$documento->id}}</b>
					<br><br>
					<b>REF.: </b>{{$tipoDoc->name}} {{$proveedor->name}}
					<br><br>
					@if($fecha != null)
						<b>SANTIAGO</b>, {{$fecha}}
					@else
						<b>MEMO PREELIMINAR, AUN NO AUTORIZADO POR JEFE DE REFERENTE TÉCNICO</b>
					@endif	
				</div>
			</div>
			</br></br>
			<div class="body">
				<div class="titulo">
					<div class="left">DE:</div>
					<div class="right">
					@if($user != null)
						{{mb_strtoupper($user->name)}}
					@endif
					</div>
					<div class="left"></div>
					<div class="right">JEFE(A) DEPARTAMENTO DE {{mb_strtoupper($referente->name)}}</div>
				</div>
				<div class="titulo">
					<div class="left">A:</div>
					<div class="right">{{mb_strtoupper($user3->name)}}</div>
					<div class="left"></div>
					<div class="right">JEFE(A) DEPARTAMENTO DE CONVENIO</div>
				</div>
				<br>
				<div class="detalle">
					Mediante el presente Memorándum, remito a usted Facturas y/o Boletas de acuerdo al siguiente detalle: 
				</div>
				<br>
				<div class="detalle">
					<table>
						<tr>
							<td>EMPRESA</td>
							<td>N° DOCUMENTO</td>
							<td>MES/AÑO</td>
							<td>MONTO</td>
						</tr>
						<tr>
							<td>{{$proveedor->name}}</td>
							<td>{{$documento->nDoc}}</td>
							<td>{{$fecha2}}</td>
							<td>{{$documento->monto}}</td>
						</tr>
					</table>
				</div>
				<br>
				<div class="detalle">
					Se da conformidad y VºBº técnico para el pago de las facturas señaladas, las cuales fueron previamente revisada 
					por el (la) Señor(a) {{$user2->name}}.
				</div>
				<br>
				<div class="detalle">
					{{$documento->memoGlosa}}
				</div>
				<br>
				<div class="detalle">
					Sin otro particular, saluda atentamente a usted,
				</div>
				<br>
				@if($user != null)
					<div class="code">
						{!! DNS2D::getBarcodeHTML($user->name, "QRCODE",4,4) !!}
					</div>	
					<br>
					<div class="titulo" style="text-align:center">
						{{mb_strtoupper($user->name)}}
						<br>
						JEFE(A) DEPARTAMENTO DE {{mb_strtoupper($referente->name)}}
					</div>
				@endif	
			</div>
		</div>
	</body>
</html>

