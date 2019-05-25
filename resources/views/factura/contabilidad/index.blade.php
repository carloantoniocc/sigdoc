@extends('layouts.app')

@section('content')
<div class="container-fluid">
	<!--Mensajes de Actualización de Documentos-->
	<?php $message=Session::get('message') ?>
	@if($message == 'envio')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Se Han Enviado los Documentos Exitosamente
		</div>
	@elseif($message == 'envioWarning')
		<div class="alert alert-warning alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			No se han enviado documentos sin devengo o en Estado distinto a "En Contabilidad"
		</div>	
	@elseif($message == 'devengo')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Se Ha Devengado el Documento
		</div>
	@elseif($message == 'rechazo')
		<div class="alert alert-warning alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Se Ha Rechazado el Documento
		</div>	
	@elseif($message == 'devolver')
		<div class="alert alert-warning alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Se Ha Devuelto el Documento a Convenios
		</div>	
	@endif
	<!--FIN Mensajes de Actualización de Documentos-->
	
    <div class="row">
		<div class="col-lg-10 col-lg-offset-1 col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Contabilidad</div>
                <div class="panel-body">
					<!-- row Busquedas -->
					<div class="row">						
						<!-- Filtro de Numero de Documento -->
						<form class="form-horizontal" role="form" method="GET" action="{{ URL::to('contabilidad') }}">
							<div class="col-md-3">
								<div class="input-group">
									<input id="searchNdoc" name="searchNdoc" type="number" min="0" max="2147483646" class="form-control input-sm" placeholder="Buscar Número de Documento">
									<span class="input-group-btn ">
										<button class="btn btn-default btn-sm" type="submit">Ir</button>
									</span>
								</div>
							</div>
							<!-- Filtro de Rut -->
							<div class="col-md-3">
								<div class="input-group">
									<input id="searchRut" name="searchRut" type="text" class="form-control input-sm" maxlength="8" placeholder="Buscar RUT (sin puntos ni dígito verificador)">
									<span class="input-group-btn ">
										<button class="btn btn-default btn-sm" type="submit">Ir</button>
									</span>
								</div>
							</div>
							<!-- Filtro de Tipo de Documento -->
							<div class="col-md-3">
								<div class="input-group">
									<select id="searchTipo" class="form-control input-sm" name="searchTipo" style="width:100%">
										<option value="">Buscar por Tipo de Documento</option>
										@foreach($tipos as $tipo)
											<option value="{{ $tipo->id }}">{{ $tipo->name }}</option>
										@endforeach
									</select>
									<span class="input-group-btn ">
										<button class="btn btn-default btn-sm" type="submit">Ir</button>
									</span>
								</div>
							</div>
							<!-- Filtro de Estados -->
							<div class="col-md-3">
								<div class="input-group">
									<select id="searchEstado" class="form-control input-sm" name="searchEstado" style="width:100%">
										<option value="">Buscar por Estado</option>
										<option value="NP">Recepción Secretaría de Convenios</option>
										<option value="CV">Asignar a Referente Técnico</option>
										<option value="VB">Referente Técnico - Por Validar</option>
										<option value="RT">Jefe Referete Técnico - Por Validar</option>
										<option value="RC">Documento Validado por Referente Técnico</option>
										<option value="DE">En Contabilidad</option>
										<option value="DV">Devuelto Referente Técnico a Convenios</option>
										<option value="DR">Devuelto Jefe R.T. a Referente Técnico</option>
										<option value="DO">Devuelto Convenios a Referente Técnico</option>
										<option value="DC">Devuelto Contabilidad a Convenios</option>
									</select>
									<span class="input-group-btn ">
										<button class="btn btn-default btn-sm" type="submit">Ir</button>
									</span>
								</div>
							</div>	
						</form>		
					</div>
					<!-- FIN row Busquedas -->
					</br> 
					<!-- Lista de Documentos -->		
					<div class="row">
						<form class="form-horizontal" role="form" method="POST" action="{{ URL::to('contabilidad/enviar') }}">
							{{ csrf_field() }} 
							<div class="col-md-12">
								<table class="table table-striped">
									<thead>
									  <tr>
										<th></th>
										<th>Proveedor</th>
										<th>Tipo de Documento</th>
										<th>Nro de Documento</th>
										<th>Monto</th>
										<th>Fecha de Recepcion</th>
										<th>Orden de Compra</th>
										<th>Departamento</th>
										<th>Estado</th>
										<th>Archivo</th>
										<th>Acciones</th>
									  </tr>
									</thead>
									<tbody>
									  @foreach($documentos as $documento)
									  <tr>
										<td><input type="checkbox" name="check_list[]" id="check_list[]" value="{{$documento->id}}"></td>
										<td>{{ $documento->rut }} <br> {{ $documento->nameProveedor }}</td>
										<td>{{ $documento->tipoDoc }}</td>
										<td>{{ $documento->nDoc }}</td>
										<td>{{ number_format($documento->monto,0,",",".") }}</td>
										<td>{{ $documento->fechaRecepcion }}</td>
										<td>{{ $documento->ordenCompra }}</td>
										<td>{{ $documento->referente }}</td>
										<td>
											@if( $documento->estado == 'VB' )
												Referente Técnico - Por Validar	
											@elseif( $documento->estado == 'RT' )
												Jefe Referete Técnico - Por Validar	
											@elseif( $documento->estado == 'RC' )
												Documento Validado por Referente Técnico
											@elseif( $documento->estado == 'DE' )
												En Contabilidad	
											@elseif( $documento->estado == 'DV' )
												Devuelto Referente Técnico a Convenios	
											@elseif( $documento->estado == 'DR' )
												Devuelto Jefe R.T. a Referente Técnico	
											@elseif( $documento->estado == 'DO' )
												Devuelto Convenios a Referente Técnico	
											@elseif( $documento->estado == 'DC' )
												Devuelto Contabilidad a Convenios		
											@endif
											<!--Agrega marca de Devengo en caso de que el documento se ha devengado-->
											@if( $documento->devengo_item != null )
												<br>
												<span class="label label-info">Devengado</span>
											@endif	
										</td>
										@if( $documento->archivo == null )
											<td><span class="label label-danger">No Adjunto</span></td>
										@else	
											<td>
												<a href="{{ asset($documento->archivo) }}" target="_blank">
													<span class="label label-success"> Adjunto   </span>
												</a>
											</td>
										@endif
										<td>
											<a href="{{ URL::to('contabilidad/' . $documento->id . '/devengar') }}{{ '?searchNdoc='.$searchNdoc.'&searchRut='.$searchRut.'&searchTipo='.$searchTipo.'&searchEstado='.$searchEstado.'&page='.$page }}" title="Devengar">Devengar</a> 
											<br>
											<a href="{{ URL::to('contabilidad/' . $documento->id . '/validadores') }}" title="Revisar Validadores">Validadores</a> 
											@if( $documento->estado == 'DE' )
												<br>		
												<a href="{{ URL::to('contabilidad/' . $documento->id . '/devolver') }}" title="Devolver a Convenios">Devolver</a> 
												<br>
												<a href="{{ URL::to('contabilidad/' . $documento->id . '/rechazar/5') }}">Rechazar</a>
											@endif	
										</td>
									  </tr>
									  @endforeach
									</tbody>
								</table>
								<!--paginacion-->
								{{ $documentos->links() }}
							</div>
							<div class="col-md-3">
								<input type="submit" name="send" id="send" value="Enviar Documentos" class="btn btn-primary btn-sm"></input>
							</div>		
						</form>	
					</div>
					<!-- FIN Lista de Documentos -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection