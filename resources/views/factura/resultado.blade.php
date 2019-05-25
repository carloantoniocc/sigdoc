@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Consulta Documento</div>
                <div class="panel-body">
					
					<div class="row">
						{{ csrf_field() }} 
						<div class="col-md-6">
							<form class="form-horizontal" role="form" method="GET" action="{{ URL::to('documentos/excel') }}">
								<input type="hidden" name="proveedor" id="proveedor" value="{{$proveedor}}"> 
								<input type="hidden" name="nomDoc" id="nomDoc" value="{{$nomDoc}}"> 
								<input type="hidden" name="nomina" id="nomina" value="{{$nomina}}"> 
								<input type="hidden" name="tipo" id="tipo" value="{{$tipo}}"> 
								<input type="hidden" name="monto" id="monto" value="{{$monto}}"> 
								<input type="hidden" name="ordenCompra" id="ordenCompra" value="{{$ordenCompra}}"> 
								<input type="hidden" name="referente" id="referente" value="{{$referente}}"> 
								<input type="hidden" name="desde" id="desde" value="{{$desde}}"> 
								<input type="hidden" name="hasta" id="hasta" value="{{$hasta}}"> 
								<input type="hidden" name="estado" id="estado" value="{{$estado}}"> 
								<input type="hidden" name="devengo" id="devengo" value="{{$devengo}}"> 
								<button class="btn btn-sm btn-primary" type="submit">Exportar a Excel</button>
								<span class="label label-default">(Hasta 5000 registros)</span>
							</form>
						</div>
					</div>
					</br>
					<!-- Lista de Documentos -->		
					<div class="row">
						<div class="col-md-12">
							<table class="table table-striped">
								<thead>
								  <tr>
									<th>Proveedor</th>
									<th>Tipo de Documento</th>
									<th>Nro de Documento</th>
									<th>Nómina</th>
									<th>Monto</th>
									<th>Fecha de Recepcion</th>
									<th>Referente Técnico</th>
									<th>Convenio</th>
									<th>Establecimiento</th>
									<th>Estado</th>
									<th>Destino</th>
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
									<td>{{ $documento->nomina }}</td>
									<td>{{ $documento->monto }}</td>
									<td>{{ $documento->fechaRecepcion }}</td>
									<td>{{ $documento->referente }}</td>
									<td>{{ $documento->convenio }}</td>
									<td>{{ $documento->establecimiento }}</td>
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
									
									<td>
									@if( $documento->destino == null )
										-
									@else	
										{{ $documento->destino }}
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
										<a href="{{ URL::to('documentos/' . $documento->id . '/validadores') }}" title="Revisar Validadores">Validadores</a> 
										<br>
										<a href="{{ URL::to('documentos/' . $documento->id . '/bitacora') }}" title="Bitácora de Documento">Bitácora</a> 
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