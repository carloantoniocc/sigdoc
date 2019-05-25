@extends('layouts.app')

@section('content')
<div class="container-fluid">
	<!--Mensajes de Asignacion de Nominas o Actualización de Documentos-->
	<?php $message=Session::get('message') ?>
	@if($message == 'nomina')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Nomina Creada Exitosamente
		</div>
	@elseif($message == 'nominaWarning')
		<div class="alert alert-warning alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Nomina Creada. No se generó nómina a documentos sin archivo adjunto.
		</div>
	@elseif($message == 'envioNomina')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Documentos enviados secretaría de convenios
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
	<!--FIN Mensajes de Asignacion de Nominas o Actualización de Documentos-->
	
	
	
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Asignar Nomina</div>
                <div class="panel-body">
					<!-- row Busquedas -->
					<div class="row">
						<!-- Boton Enviar Documentos -->
						<div class="col-md-2">
							<form class="form-horizontal" role="form" method="GET" action="{{ URL::to('oficinaPartes/enviaNomina') }}">
								<input type="submit" name="send" id="send" value="Enviar Documentos" class="btn btn-sm btn-primary"></input>
							</form>
						</div>						
						<!-- Filtro de Numero de Documento -->
						<div class="col-md-3 col-md-offset-1">
							<form class="form-horizontal" role="form" method="GET" action="{{ URL::to('oficinaPartes/nomina') }}">
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
							<form class="form-horizontal" role="form" method="GET" action="{{ URL::to('oficinaPartes/nomina') }}">
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
							<form class="form-horizontal" role="form" method="GET" action="{{ URL::to('oficinaPartes/nomina') }}">
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
					<!-- Lista de Documentos -->		
					<div class="row">
						<form class="form-horizontal" role="form" method="POST" action="{{ URL::to('oficinaPartes/generarNomina') }}">
							{{ csrf_field() }} 
							<div class="col-md-12">
								<table class="table table-striped">
									<thead>
									  <tr>
										<th>Id</th>
										<th><input type="checkbox" name="check_all" id="check_all"></th>
										<th>Proveedor</th>
										<th>Tipo de Documento</th>
										<th>Nro de Documento</th>
										<th>Fecha de Recepcion</th>
										<th>Orden de Compra</th>
										<th>Nómina</th>
										<th>Archivo</th>
										<th>Editar</th>
									  </tr>
									</thead>
									<tbody>
									@php
										$aux = 1;
									@endphp	
									@foreach($documentos as $documento)
										<tr>
											<td>{{ $aux }}</td>
											<td><input type="checkbox" name="check_list[]" id="check_list[]" value="{{$documento->id}}"></td>
											<td>{{ $documento->rut }} | {{ $documento->nameProveedor }}</td>
											<td>{{ $documento->tipoDoc }}</td>
											<td>{{ $documento->nDoc }}</td>
											<td>{{ $documento->fechaRecepcion }}</td>
											<td>{{ $documento->ordenCompra }}</td>
											<td>{{ $documento->nomina }}</td>
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
												<a href="{{ URL::to('oficinaPartes/' . $documento->id . '/edit/1') }}">Editar</a> | 
												<a href="{{ URL::to('oficinaPartes/' . $documento->id . '/rechazar/1') }}">Rechazar</a>
											</td>
										</tr>
										@php
											$aux++;
										@endphp
									@endforeach
									</tbody>
								</table>
								<!--paginacion-->
								{{ $documentos->links() }}
							</div>
							<div class="col-md-3">
								<button type="submit" class="btn btn-primary btn-sm">
									Generar Nómina
								</button>
							</div>		
						</form>
					</div>		
					<!-- FIN Lista de Documentos -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- SE SELECCIONAN TODOS LOS DOCUMENTOS -->
<script>
document.getElementById('check_all').addEventListener("change", function(){
	var checks = document.getElementsByName("check_list[]");
	
	if ( this.checked == true) {
		for (var i=0; i < checks.length; i++) {
			checks[i].checked = true;
		}
	}
	else {	
		for (var i=0; i < checks.length; i++) {
			checks[i].checked = false;
		}
	}	
});	
</script>
@endsection