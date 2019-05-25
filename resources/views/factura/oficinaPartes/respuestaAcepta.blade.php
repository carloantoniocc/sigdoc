@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Resultado Carga de Documentos desde Acepta</div>
                <div class="panel-body">
					{{ csrf_field() }} 
					<div class="row row-padding row-border">
						<div class="col-md-12">
							<div class="alert alert-success" role="alert">
								Se ingresaron correctamente <strong>{{ $cont }} documentos</strong>. Revisar en Oficina de Partes -> Nóminas
							</div>
						</div>
					</div>	
					<!-- Lista de Documentos No Ingresados-->
					<div class="row row-border">
						<div class="col-md-12">
							<h5><strong>Documentos No Cargados</strong></h5>
						</div>
					</div>
					<br>
					<!--Error1: Tipo de Documento No Existe-->
					@if($error1 > 0)
						<div class="row row-border">
							<div class="col-xs-10 col-sm-11 col-md-11">
								<h5>Tipo de Documento no Existe <strong>({{$error1}} encontrados)</strong>.</h5>
							</div>
							<div class="col-xs-1 col-sm-1 col-md-1">
								<h5>
									<a href="#" class="pull-right"  data-toggle="collapse" data-target="#error1">
										<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h5>
							</div>
						</div>
						</br>
						<div id="error1" class="collapse">
							<div class="row row-padding">
								<div class="col-md-12">
									<table class="table table-striped">
										<thead>
											<tr>
												<th>Estado</th>
												<th>Proveedor</th>
												<th>Tipo de Documento</th>
												<th>Número de Documento</th>
												<th>Observación</th>
											</tr>
										</thead>
										<tbody>
											@foreach($respuestas as $respuesta)
												@php
													$respuesta = explode("::", $respuesta);
												@endphp
												@if($respuesta[5] == 1)
													<tr>
														<td><span class="label label-danger">No Cargado</span></td>
														<td>{{ $respuesta[0] }} | {{ $respuesta[1] }}</td>
														<td>{{ $respuesta[2] }}</td>
														<td>{{ $respuesta[3] }}</td>
														<td>{{ $respuesta[4] }}</td>
													</tr>
												@endif	
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						</div>
					@endif
					<!-- FIN Error1 -->
					<!--Error2: Proveedor No Existe-->
					@if($error2 > 0)
						<div class="row row-border">
							<div class="col-xs-10 col-sm-11 col-md-11">
								<h5>Proveedor no Existe <strong>({{$error2}} encontrados)</strong>.</h5>
							</div>
							<div class="col-xs-1 col-sm-1 col-md-1">
								<h5>
									<a href="#" class="pull-right"  data-toggle="collapse" data-target="#error2">
										<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h5>
							</div>
						</div>
						</br>
						<div id="error2" class="collapse">
							<div class="row row-padding">
								<div class="col-md-12">
									<table class="table table-striped">
										<thead>
											<tr>
												<th>Estado</th>
												<th>Proveedor</th>
												<th>Tipo de Documento</th>
												<th>Número de Documento</th>
												<th>Observación</th>
											</tr>
										</thead>
										<tbody>
											@foreach($respuestas as $respuesta)
												@php
													$respuesta = explode("::", $respuesta);
												@endphp
												@if($respuesta[5] == 2)
													<tr>
														<td><span class="label label-danger">No Cargado</span></td>
														<td>{{ $respuesta[0] }} | {{ $respuesta[1] }}</td>
														<td>{{ $respuesta[2] }}</td>
														<td>{{ $respuesta[3] }}</td>
														<td>{{ $respuesta[4] }}</td>
													</tr>
												@endif	
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						</div>
					@endif
					<!-- FIN Error2 -->
					<!--Error3: Documento Duplicado-->
					@if($error3 > 0)
						<div class="row row-border">
							<div class="col-xs-10 col-sm-11 col-md-11">
								<h5>Documento Duplicado <strong>({{$error3}} encontrados)</strong>.</h5>
							</div>
							<div class="col-xs-1 col-sm-1 col-md-1">
								<h5>
									<a href="#" class="pull-right"  data-toggle="collapse" data-target="#error3">
										<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h5>
							</div>
						</div>
						</br>
						<div id="error3" class="collapse">
							<div class="row row-padding">
								<div class="col-md-12">
									<table class="table table-striped">
										<thead>
											<tr>
												<th>Estado</th>
												<th>Proveedor</th>
												<th>Tipo de Documento</th>
												<th>Número de Documento</th>
												<th>Observación</th>
											</tr>
										</thead>
										<tbody>
											@foreach($respuestas as $respuesta)
												@php
													$respuesta = explode("::", $respuesta);
												@endphp
												@if($respuesta[5] == 3)
													<tr>
														<td><span class="label label-danger">No Cargado</span></td>
														<td>{{ $respuesta[0] }} | {{ $respuesta[1] }}</td>
														<td>{{ $respuesta[2] }}</td>
														<td>{{ $respuesta[3] }}</td>
														<td>{{ $respuesta[4] }}</td>
													</tr>
												@endif	
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						</div>
					@endif
					<!-- FIN Error3 -->
					<!--Error4: Fecha de Emisión-->
					@if($error4 > 0)
						<div class="row row-border">
							<div class="col-xs-10 col-sm-11 col-md-11">
								<h5>Fecha de Emisión No Existe <strong>({{$error4}} encontrados)</strong>.</h5>
							</div>
							<div class="col-xs-1 col-sm-1 col-md-1">
								<h5>
									<a href="#" class="pull-right"  data-toggle="collapse" data-target="#error4">
										<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h5>
							</div>
						</div>
						</br>
						<div id="error4" class="collapse">
							<div class="row row-padding">
								<div class="col-md-12">
									<table class="table table-striped">
										<thead>
											<tr>
												<th>Estado</th>
												<th>Proveedor</th>
												<th>Tipo de Documento</th>
												<th>Número de Documento</th>
												<th>Observación</th>
											</tr>
										</thead>
										<tbody>
											@foreach($respuestas as $respuesta)
												@php
													$respuesta = explode("::", $respuesta);
												@endphp
												@if($respuesta[5] == 4)
													<tr>
														<td><span class="label label-danger">No Cargado</span></td>
														<td>{{ $respuesta[0] }} | {{ $respuesta[1] }}</td>
														<td>{{ $respuesta[2] }}</td>
														<td>{{ $respuesta[3] }}</td>
														<td>{{ $respuesta[4] }}</td>
													</tr>
												@endif	
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						</div>
					@endif
					<!-- FIN Error4 -->
					<!--Error5: Adjuntar Documento-->
					@if($error5 > 0)
						<div class="row row-border">
							<div class="col-xs-10 col-sm-11 col-md-11">
								<h5>Error al adjuntar documentos <strong>({{$error5}} encontrados)</strong>.</h5>
							</div>
							<div class="col-xs-1 col-sm-1 col-md-1">
								<h5>
									<a href="#" class="pull-right"  data-toggle="collapse" data-target="#error5">
										<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h5>
							</div>
						</div>
						</br>
						<div id="error5" class="collapse">
							<div class="row row-padding">
								<div class="col-md-12">
									<table class="table table-striped">
										<thead>
											<tr>
												<th>Estado</th>
												<th>Proveedor</th>
												<th>Tipo de Documento</th>
												<th>Número de Documento</th>
												<th>Observación</th>
											</tr>
										</thead>
										<tbody>
											@foreach($respuestas as $respuesta)
												@php
													$respuesta = explode("::", $respuesta);
												@endphp
												@if($respuesta[5] == 5)
													<tr>
														<td><span class="label label-danger">No Cargado</span></td>
														<td>{{ $respuesta[0] }} | {{ $respuesta[1] }}</td>
														<td>{{ $respuesta[2] }}</td>
														<td>{{ $respuesta[3] }}</td>
														<td>{{ $respuesta[4] }}</td>
													</tr>
												@endif	
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						</div>
					@endif
					<!-- FIN Error5 -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection