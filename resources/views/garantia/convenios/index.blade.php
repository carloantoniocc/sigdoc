@extends('layouts.app')

@section('content')
<div class="container-fluid">
	<!--Mensajes de Actualización de Documentos-->
	<?php $message=Session::get('message') ?>
	@if($message == 'renovado')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			La Fecha de Vencimiento del Documento ha sido Renovada
		</div>
	@elseif($message == 'envio')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Documentos han sido enviados a Finanzas
		</div>
	@elseif($message == 'update')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Documento Modificado Exitosamente
		</div>
	@elseif($message == 'rechazo')
		<div class="alert alert-warning alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Se Ha Rechazado el Documento
		</div>	
	@endif
	<!--FIN Mensajes de Actualización de Documentos-->
	
	
	
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Documentos de Garantía</div>
                <div class="panel-body">
					<!-- row Busquedas -->
					<div class="row">					
						<!-- Filtro de Numero de Documento -->
						<div class="col-md-3 col-md-offset-3">
							<form class="form-horizontal" role="form" method="GET" action="{{ URL::to('garantias/convenios') }}">
								<div class="input-group">
									<input id="searchNdoc" name="searchNdoc" type="number" min="0" max="2147483646" class="form-control input-sm" placeholder="Buscar Número de Documento">
									<span class="input-group-btn ">
										<button class="btn btn-default btn-sm" type="submit">Ir</button>
									</span>
								</div>
							</form>
						</div>
						<!-- Filtro de Rut -->
						<div class="col-md-3">
							<form class="form-horizontal" role="form" method="GET" action="{{ URL::to('garantias/convenios') }}">
								<div class="input-group">
									<input id="searchRut" name="searchRut" type="text" class="form-control input-sm" maxlength="8" placeholder="Buscar RUT (sin puntos ni dígito verificador)">
									<span class="input-group-btn ">
										<button class="btn btn-default btn-sm" type="submit">Ir</button>
									</span>
								</div>
							</form>
						</div>
						<!-- Filtro de Tipo de Documento -->
						<div class="col-md-3">
							<form class="form-horizontal" role="form" method="GET" action="{{ URL::to('garantias/convenios') }}">
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
							</form>
						</div>		
								
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
									<th>Objeto de Garantía</th>
									<th>Licitación</th>
									<th>Fecha de Recepcion</th>
									<th>Fecha de Vencimiento</th>
									<th>Observación</th>
									<th>Adjunto</th>
									<th>Acciones</th>
								  </tr>
								</thead>
								<tbody>
								  @foreach($garantias as $garantia)
								  <tr>
									<td>{{ $garantia->rut }} | {{ $garantia->nameProveedor }}</td>
									<td>{{ $garantia->tipoDoc }}</td>
									<td>{{ $garantia->nDoc }}</td>
									<td>{{ $garantia->monto }} {{ $garantia->moneda }}</td>
									<td>{{ $garantia->objeto_garantia }}</td>
									<td>{{ $garantia->licitacion }}</td>
									<td>{{ $garantia->fechaRecepcion }}</td>
									<td>{{ $garantia->fechaVencimiento }}</td>
									<td>
										@if( $garantia->observacion != null )
											<a href="#" onClick="show('{{ $garantia->observacion }}')">Ver</a>
										@else
											Sin Observación
										@endif
									</td>
									<td>
										<a href="{{ asset($garantia->archivo) }}" target="_blank">
										<span class="label label-success"> Adjunto   </span>
									</td>
									<td>
										@if( $garantia->estado == 'NP' )
											<a href="{{ URL::to('garantias/convenios/' . $garantia->id . '/edit/2') }}">Editar</a>       <br>										
											<a href="{{ URL::to('garantias/convenios/' . $garantia->id . '/rechazar/2') }}">Rechazar</a> <br>
										@endif	
										<a href="{{ URL::to('garantias/convenios/' . $garantia->id . '/renovar/2') }}">Renovar</a>  <br>
										<a href="{{ URL::to('garantias/convenios/' . $garantia->id . '/enviar/2') }}">Enviar</a> 
									</td>
								  </tr>
								  @endforeach
								</tbody>
							</table>
							<!--paginacion-->
							{{ $garantias->links() }}
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