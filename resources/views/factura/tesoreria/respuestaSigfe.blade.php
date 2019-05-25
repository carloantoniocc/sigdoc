@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Resultado Documentos Carga Masiva</div>
	            <div class="panel-body">
					{{ csrf_field() }} 
					<div class="row row-padding row-border">
						<div class="col-md-12">
							<div class="alert alert-success alert-dismissible" role="alert">
								Se ingresaron correctamente {{ $cont }} documentos.
							</div>
						</div>
					</div>					
					<!-- Lista de Documentos No Ingresados-->
					<div class="row">
						<div class="col-md-12">
							<h5><strong>Documentos No Cargados</strong></h5>
						</div>
					</div>
					<!-- Botón para exportar a excel-->						
					<div class="row">
						{{ csrf_field() }} 
						<div class="col-md-9">							
							<form class="form-horizontal" role="form" method="GET" action="{{ URL::to('excelRespuestaSIGFE') }}">	
								<input type="submit" name="send_excel" id="send_excel" value="Exportar a Excel" class="btn btn-sm btn-primary"></input>								                 
	                        </form>
						</div>
					</div>	
					<br>
					<!--Error1: Error de Formato-->
					@if($error1 > 0)
						<div class="row row-border">
							<div class="col-xs-10 col-sm-11 col-md-11">
								<h5>Error de Formato <strong>({{$error1}} encontrados)</strong>.</h5>
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
												<th>Número de Documento</th>
												<th>Monto</th>
												<th>Observación</th>
											</tr>
										</thead>
										<tbody>
											@foreach($respuestas as $respuesta)
												@php
													$respuesta = explode("::", $respuesta);
												@endphp
												@if($respuesta[4] == 1)
													<tr>
														<td><span class="label label-danger">No Cargado</span></td>
														<td>{{ $respuesta[0] }}</td>
														<td>{{ $respuesta[1] }}</td>
														<td>{{ $respuesta[2] }}</td>
														<td>{{ $respuesta[3] }}</td>
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
					<!--Error2: Proveedor no Existe-->
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
												<th>Número de Documento</th>
												<th>Monto</th>
												<th>Observación</th>
											</tr>
										</thead>
										<tbody>
											@foreach($respuestas as $respuesta)
												@php
													$respuesta = explode("::", $respuesta);
												@endphp
												@if($respuesta[4] == 2)
													<tr>
														<td><span class="label label-danger">No Cargado</span></td>
														<td>{{ $respuesta[0] }}</td>
														<td>{{ $respuesta[1] }}</td>
														<td>{{ $respuesta[2] }}</td>
														<td>{{ $respuesta[3] }}</td>
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
					<!--Error3: Error Documento-->
					@if($error3 > 0)
						<div class="row row-border">
							<div class="col-xs-10 col-sm-11 col-md-11">
								<h5>Error en Documentos <strong>({{$error3}} encontrados)</strong>.</h5>
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
												<th>Número de Documento</th>
												<th>Monto</th>
												<th>Observación</th>
											</tr>
										</thead>
										<tbody>
											@foreach($respuestas as $respuesta)
												@php
													$respuesta = explode("::", $respuesta);
												@endphp
												@if($respuesta[4] == 3)
													<tr>
														<td><span class="label label-danger">No Cargado</span></td>
														<td>{{ $respuesta[0] }}</td>
														<td>{{ $respuesta[1] }}</td>
														<td>{{ $respuesta[2] }}</td>
														<td>{{ $respuesta[3] }}</td>
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
					<!-- FIN Lista de Documentos -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection