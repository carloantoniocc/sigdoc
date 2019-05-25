@extends('layouts.app')

@section('content')
<div class="container-fluid">
	<!--Mensajes de Actualización de Documentos-->
	<?php $message=Session::get('message') ?>
	@if($message == 'update')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Se Han Modificado el Documento
		</div>
	@elseif($message == 'reversa')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Se Han Reversado el Estado del Documento
		</div>	
	@endif
	<!--FIN Mensajes de Actualización de Documentos-->
	
    <div class="row">
		<div class="col-lg-10 col-lg-offset-1 col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Administrador - Editar Documento</div>
                <div class="panel-body">
					<!-- row Busquedas -->
					<div class="row">						
						<!-- Filtro de Numero de Documento -->
						<form class="form-horizontal" role="form" method="GET" action="{{ URL::to('administrador/documentos') }}">
							<div class="col-md-3 col-md-offset-3">
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
						</form>		
					</div>
					<!-- FIN row Busquedas -->
					</br> 
					<!-- Lista de Documentos -->		
					<div class="row">
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
									<td>
										@php
											switch ($documento->estado) {
												case 'OP': echo "Ingresado Oficina de Partes"; break;
												case 'NP': echo "Recepción Secretaría de Convenios"; break;	
												case 'CV': echo "Asignar a Referente Técnico"; break;
												case 'VB': echo "Referente Técnico - Por Validar"; break;
												case 'RT': echo "Jefe Referente Técnico - Por Validar"; break;
												case 'RC': echo "Documento Validado por Referente Técnico"; break;
												case 'DE': echo "En Contabilidad"; break;
												case 'TE': echo "En Tesorería para Pago"; break;
												case 'EN': echo "En Tesorería para Entrega"; break;
												case 'DV': echo "Devuelto Referente Técnico a Convenios"; break;
												case 'DR': echo "Devuelto Jefe R.T. a Referente Técnico"; break;	 
												case 'DO': echo "Devuelto Convenios a Referente Técnico"; break;
												case 'DC': echo "Devuelto Contabilidad a Convenios"; break;
												case 'FN': echo "Entregado"; break;
												case 'RE': echo "Rechazado"; break;													
											}
										@endphp
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
										<a href="{{ URL::to('administrador/' . $documento->id . '/edit/0') }}" title="Editar">Editar</a> 
										@if( $documento->estado <> 'CV' )
											<br>
											<a href="{{ URL::to('administrador/' . $documento->id . '/reversar') }}" title="Reversar">Reversar</a> 
										@endif	
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
@endsection