@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
			<!--Panel Formulario Crear Documento-->
			<div class="panel panel-default">
                <div class="panel-heading">Reversar Último Movimiento de Documentos</div>
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
									<th>Estado Actual</th>
									<th>Estado a Reversar</th>
								  </tr>
								</thead>
								<tbody>
								  <tr>
									<td>{{ $proveedor->rut }}-{{ $proveedor->dv }} | {{ $proveedor->name }}</td>
									<td>{{ $tipo->name }}</td>
									<td>{{ $documento->nDoc }}</td>
									<td>{{ $documento->fechaRecepcion }}</td>
									<td>{{ $documento->ordenCompra }}</td>
									<td>
										@php
											switch ($movActual) {
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
										@php
											switch ($movAnt) {
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
								  </tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="row">	
						<div class="col-md-10 col-md-offset-1">
							<form role="form" method="POST" action="{{ URL::to('administrador/reversar') }}">
								{{ csrf_field() }}
								<!--Campo Nombre-->
								<div class="form-group">
									<label for="name" class="control-label">Observacion de Reverso de Movimiento</label>
									<textarea class="form-control" rows="5" id="comment" id="observacion" name="observacion" maxlength="150" placeholder="Observación" autofocus required></textarea>
									<!--Elementos ocultos-->
									<input name="documento_id" id="documento_id" type="hidden" value="{{ $documento->id }}">
								</div>
								<div class="form-group">
									<button type="submit" class="btn btn-danger">
										Reversar
									</button>
								</div>
							</form>
						</div>
					</div>	
				</div>
            </div>
			<!--FIN Panel Formulario Documento-->
        </div>
    </div>
</div>
@endsection

