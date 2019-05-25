@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
			<!--Panel Formulario Crear Documento-->
			<div class="panel panel-default">
                <div class="panel-heading">Documentos Validadores</div>
                <div class="panel-body">
					<!-- Datos del Documento -->		
					<div class="row">	
						<div class="col-md-10 col-md-offset-1">
							<table class="table table-striped">
								<thead>
								  <tr>
									<th>Proveedor</th>
									<th>Tipo de Documento</th>
									<th>Nro de Documento</th>
									<th>Fecha de Recepcion</th>
									<th>Orden de Compra</th>
								  </tr>
								</thead>
								<tbody>
								  <tr>
									<td>{{ $proveedor->rut }}-{{ $proveedor->dv }} | {{ $proveedor->name }}</td>
									<td>{{ $tipo->name }}</td>
									<td>{{ $documento->nDoc }}</td>
									<td>{{ $documento->fechaRecepcion }}</td>
									<td>{{ $documento->ordenCompra }}</td>
								  </tr>
								</tbody>
							</table>
						</div>
					</div>
					<br>
					<div class="row">	
						<div class="col-md-10 col-md-offset-1">						
							{{ csrf_field() }}
							<table class="table table-striped">
								<thead>
								  <tr>
									<th>Documento</th>
									<th>Archivo</th>
								  </tr>
								</thead>
								<!--VALIDADORES ADJUNTOS-->
								@foreach($validadores as $validador)
								<tr>
									<td>{{ $validador->name }}</td>
									<td>
										@if( $validador->archivo == null )
											<span class="label label-danger">No Adjunto</span>
										@else	
											<a href="{{ asset($validador->archivo) }}" target="_blank">
												<span class="label label-success"> Adjunto   </span>
											</a>
										@endif
									</td>
								</tr>
								@endforeach
								<!--MEMOS AUTOMATICOS-->
								@if( $documento->memoRef == 1 )
									@if ($movimiento->estado == 'RC' || $movimiento->estado == 'DC' || $movimiento->estado == 'DE' || $movimiento->estado == 'TE' || $movimiento->estado == 'EN' || $movimiento->estado == 'FN')
									<tr>
										<td>Memo Referente Técnico</td>
										<td>
											<a href="{{ URL::to('documentos/'.$documento->id.'/memoPdf') }}" target="_blank">
												<span class="label label-success"> Adjunto   </span>
											</a>
										</td>
									</tr>
									@endif
									
									@if ($movimiento->estado == 'RT')
									<tr>
										<td>Memo Referente Técnico (preliminar)</td>
										<td>
											<a href="{{ URL::to('documentos/'.$documento->id.'/memoPdfPre') }}" target="_blank">
												<span class="label label-success"> Adjunto   </span>
											</a>
										</td>
									</tr>
									@endif
								@endif
								@if( $documento->memoCon == 1 )		
									@if ($movimiento->estado == 'DE' || $movimiento->estado == 'TE' || $movimiento->estado == 'EN' || $movimiento->estado == 'FN')
									<tr>
										<td>Memo Oficina de Convenios</td>
										<td>
											<a href="{{ URL::to('documentos/'.$documento->id.'/memo2Pdf') }}" target="_blank">
												<span class="label label-success"> Adjunto   </span>
											</a>
										</td>
									</tr>
									@endif
								@endif	
								<!--VALIDADORES NO ADJUNTOS-->
								@foreach($validadores2 as $validador2)
								<tr>
									<td>{{ $validador2->name }}</td>
									<td><span class="label label-danger">No Adjunto</span></td>
								</tr>
								@endforeach
							</table>	
						</div>
					</div>	
				</div>
            </div>
			<!--FIN Panel Formulario Documento-->
        </div>
    </div>
</div>
<!--COMPLETA CAMPO DE ARCHIVO ARCHIVO(id)-->
<script>
function archivos(id,valor) {
	document.getElementById(id).value = valor.replace(/\\/g, '/').replace(/.*\//, '');
}
</script>
@endsection

