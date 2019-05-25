@extends('layouts.app')

@section('content')
<div class="container-fluid">
	<!--Mensajes de Actualización de Documentos-->
	<?php $message=Session::get('message') ?>
	@if($message == 'rechazo')
		<div class="alert alert-warning alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Se Ha Rechazado el Documento
		</div>
	@elseif($message == 'envio')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Se Han Enviado el Documento a Visto Bueno
		</div>		
	@endif
	<!--FIN Mensajes de Actualización de Documentos-->
	
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Envío a Vistos Buenos</div>
                <div class="panel-body">
					<!-- row Busquedas -->
					<div class="row">						
						<!-- Filtro de Numero de Documento -->
						<form class="form-horizontal" role="form" method="GET" action="{{ URL::to('oficinaConvenios/vistosBuenos') }}">
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
										<option value="CV">Asignar a Referente Técnico</option>
										<option value="DV">Devuelto Referente Técnico a Convenios</option>
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
					<!-- Observacion del Documento -->
					<div class="row">
						<div class="col-md-12">
							<div id="observacion" class="alert alert-warning alert-dismissible" role="alert" style="display: none;">
								<button type="button" class="close" onclick="$('#observacion').hide()"><span aria-hidden="true">&times;</span></button>
								<b>Observación</b>
								<p id="obsTexto" style="word-wrap: break-word;"></p>
							</div>	
						</div>
					</div>	
					<!-- Lista de Documentos -->		
					<div class="row">
						{{ csrf_field() }} 
						<div class="col-md-12">
							<table class="table table-striped">
								<thead>
								  <tr>
									<th>Proveedor</th>
									<th>Tipo de Documento</th>
									<th>Nro de Documento</th>
									<th>Monto</th>
									<th>Fecha de Recepcion</th>
									<th>Orden de Compra</th>
									<th>Estado</th>
									<th>Observación</th>
									<th>Archivo</th>
									<th>Acciones</th>
								  </tr>
								</thead>
								<tbody>
								  @foreach($documentos as $documento)
								  <tr>
									<td>{{ $documento->rut }} <br> {{ $documento->nameProveedor }}</td>
									<td>{{ $documento->tipoDoc }}</td>
									<td>{{ $documento->nDoc }}</td>
									<td>{{ number_format($documento->monto,0,",",".") }}</td>
									<td>{{ $documento->fechaRecepcion }}</td>
									<td>{{ $documento->ordenCompra }}</td>
									<td>
										@if( $documento->estado == 'CV' )
											Asignar Referente Técnico
										@elseif( $documento->estado == 'DV' )
											Devuelto Referente Técnico	
										@endif	
									</td>
									<td>
										@if( $documento->observacion != null )
											<a href="#" onClick="show('{{ $documento->observacion }}')">Ver</a>
										@else
											Sin Observación
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
										<a href="{{ URL::to('oficinaConvenios/' . $documento->id . '/asignar') }}{{ '?searchNdoc='.$searchNdoc.'&searchRut='.$searchRut.'&searchTipo='.$searchTipo.'&searchEstado='.$searchEstado.'&page='.$page }}">Enviar a V°B°</a>
										<br>		
										<a href="{{ URL::to('oficinaConvenios/' . $documento->id . '/rechazar/3') }}">Rechazar</a>
									</td>
								  </tr>
								  @endforeach
								</tbody>
							</table>
							<!--paginacion-->
							{{ $documentos->links() }}
						</div>
					</div>
					<!-- FIN Lista de Documentos -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- FUNCION QUE MUESTRA LAS OBSERVACIONES DEL DOCUMENTO -->
<script>
function show (observacion) {
	$('#observacion').show();
	document.getElementById("obsTexto").innerHTML = observacion;
}
</script>
@endsection