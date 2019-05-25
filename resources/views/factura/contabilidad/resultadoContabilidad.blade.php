@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Reporte Contabilidad</div>
                <div class="panel-body">				
					
					<div class="row">
						{{ csrf_field() }} 
						<div class="col-md-9">							
							<form class="form-horizontal" role="form" method="GET" action="{{ URL::to('documentos/excelContabilidad') }}">							
								<button class="btn btn-sm btn-primary" type="submit">Exportar a Excel</button>
								<span class="label label-default">(Hasta 10.000 registros)</span>
								<input id="devengo" type="hidden" name="devengo" value="{{ $searchDevengo }}">                     
	                        </form>
						</div>	
						<!-- row Busquedas -->					
						<!-- Filtro de Estados -->						
						<div class="col-md-3">
							<form class="form-horizontal" role="form" method="GET" action="{{ URL::to('contabilidad/reporteContabilidad') }}">
								<div class="input-group">
									<select id="searchDevengo" class="form-control input-sm" name="searchDevengo" style="width:100%">										
										@if($searchDevengo=='0')
											<option value="">Devengados y No devengados</option>
											<option selected="true" value="0">No devengados</option>
											<option value="1">Devengados</option>
										@elseif($searchDevengo=='1')
											<option value="">Devengados y No devengados</option>
											<option value="0">No devengados</option>
											<option selected="true" value="1">Devengados</option>
										@else
											<option selected="true" value="">Devengados y No devengados</option>
											<option value="0">No devengados</option>
											<option value="1">Devengados</option>	
										@endif
									</select>
									<span class="input-group-btn ">
										<button class="btn btn-default btn-sm" type="submit">Ir</button>
									</span>
								</div>
							</form>	
						</div>							
						
						<!-- FIN row Busquedas -->		
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