@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Consulta Documento</div>
                <div class="panel-body">
					<!-- Lista de Documentos -->		
					<div class="row">
						<div class="col-md-12">
							<table class="table table-striped">
								<thead>
								  <tr>
									<th>Proveedor</th>
									<th>Tipo de Documento</th>
									<th>Nro de Documento</th>
									<th>Objeto de Garantía</th>
									<th>Monto</th>
									<th>Fecha de Recepcion</th>
									<th>Fecha de Vencimiento</th>
									<th>Devolución / Cobro</th>
									<th>Estado</th>
									<th>Archivo</th>
									<th>Acciones</th>
								  </tr>
								</thead>
								<tbody>
								  @foreach($garantias as $garantia)
								  <tr>
									<td>{{ $garantia->rut }} <br> {{ $garantia->nameProveedor }}</td>
									<td>{{ $garantia->tipoDoc }}</td>
									<td>{{ $garantia->nDoc }}</td>
									<td>{{ $garantia->objeto_garantia }}</td>
									<td>{{ $garantia->monto }} {{ $garantia->moneda }}</td>
									<td>{{ $garantia->fechaRecepcion }}</td>
									<td>{{ $garantia->fechaVencimiento }}</td>
									<td>
										@if ($garantia->estadoDev == 1)
											a Cobro
										@elseif($garantia->estadoDev == 2) 	
											a Devolución
										@endif
									</td>
									<td>
										@php
											switch ($garantia->estado) {
												case 'OP': echo "Ingresado Oficina de Partes"; break;
												case 'NP': echo "Recepción Convenios / Abastecimiento"; break;	
												case 'RN': echo "Renovado Convenios / Abastecimiento"; break;
												case 'TE': echo "En Tesorería"; break;
												case 'DG': echo "Devuelto a Proveedor"; break;
												case 'CG': echo "Cobrado"; break;
												case 'RE': echo "Rechazado"; break;
											}
										@endphp
									</td>
									@if( $garantia->archivo == null )
										<td><span class="label label-danger">No Adjunto</span></td>
									@else	
										<td>
											<a href="{{ asset($garantia->archivo) }}" target="_blank">
												<span class="label label-success"> Adjunto   </span>
											</a>
										</td>
									@endif
									<td>
										<a href="{{ URL::to('garantias/' . $garantia->id . '/adjuntos') }}">Documentos</a> 
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
@endsection