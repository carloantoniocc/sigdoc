@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
			<!--Panel Formulario Crear Documento-->
			<div class="panel panel-default">
                <div class="panel-heading">Bitácora de Documento</div>
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
									<th>Item de Devengo</th>
									<th>Observación de Devengo</th>
									<th>Fecha de Devengo</th>
								  </tr>
								</thead>
								<tbody>
								  <tr>
									<td>{{ $proveedor->rut }}-{{ $proveedor->dv }} | {{ $proveedor->name }}</td>
									<td>{{ $tipoDoc->name }}</td>
									<td>{{ $documento->nDoc }}</td>
									<td>{{ $documento->fechaRecepcion }}</td>
									<td>{{ $documento->ordenCompra }}</td>
									<td>
										@if	( $item != null )
											{{ $item->name }}
										@endif
									</td>
									<td>{{ $documento->devengo_observacion }}</td>
									<td>{{ $documento->devengo_fecha }}</td>
								  </tr>
								</tbody>
							</table>
						</div>
					</div>
					
					<div class="row">	
						<div class="col-md-10 col-md-offset-1">
							<table class="table table-striped">
								<thead>
								  <tr>
									
									<th>Operación de Pago</th>
									<th>Tipo de Pago</th>
									<th>Cuenta</th>
									<th>Fecha de Pago</th>
									<th>Nro SIGFE</th>
									<th>Fecha de Entrega</th>
									<th>Quien Retira</th>
								  </tr>
								</thead>
								<tbody>
								  <tr>
									
									<td>{{ $documento->pago_operacion }}</td>
									<td>
										@if ( $tipoPago != null )
											{{ $tipoPago->name }}
										@endif
									</td>
									<td>
										@if	( $cuenta != null )
											{{ $cuenta->name }}
										@endif
									</td>
									<td>{{ $documento->pago_fechaPago }}</td>
									<td>{{ $documento->pago_sigfe }}</td>
									<td>{{ $documento->entrega_fecha }}</td>
									<td>{{ $documento->entrega_rut }} | {{ $documento->entrega_nombre }} </td>
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
									<th>Estado</th>
									<th>Observación</th>
									<th>Ejecutado por</th>
									<th>Fecha de Ingreso</th>
									<th>Cantidad de Días</th>
								  </tr>
								</thead>
								@foreach($movimientos as $movimiento)
								<tr>
									<td>
									@php
										switch ($movimiento->estado) {
											case 'OP': echo "De <i>Ingresado Oficina de Partes</i> a <i>Generación Nómina</i>"; break;
											case 'NP': echo "De <i>Generación Nómina</i> a <i>Revisión Secretaria de Convenios</i>"; break;	
											case 'CV': echo "De <i>Revisión Secretaria de Convenios</b> a <i>Oficina de Convenios</i>"; break;
											case 'VB': echo "De <i>Oficina de Convenios</i> a <i>Referente Técnico - Por Validar</i>"; break;
											case 'RT': echo "De <i>Referente Técnico - Por Validar</i> a <i>Jefe Referente Técnico - Por Validar</i>"; break;
											case 'RC': echo "De <i>Jefe Referente Técnico - Por Validar</i> a <i>Oficina de Convenios - Revisión</i>"; break;
											case 'DE': echo "De <i>Oficina de Convenios - Revisión</i> a <i>Contabilidad</i>"; break;
											case 'TE': echo "De <i>Contabilidad</i> a <i>Tesorería para Pago</i>"; break;
											case 'EN': echo "De <i>Tesorería para Pago</i> a <i>Tesorería para Entrega</i>"; break;
											case 'DV': echo "<i>Devuelto por Referente Técnico a Convenios</i>"; break;
											case 'DR': echo "<i>Devuelto por Jefe R.T. a Referente Técnico</i>"; break;	 
											case 'DO': echo "<i>Devuelto Convenios a Referente Técnico</i>"; break;
											case 'DC': echo "<i>Devuelto Contabilidad a Convenios</i>"; break;
											case 'FN': echo "<i>Entregado</i>"; break;
											case 'RE': echo "<i>Rechazado</i>"; break;													
										}
									@endphp
									</td>
									<td>{{ $movimiento->observacion }}</td>
									<td>{{ $movimiento->user }}</td>
									<td>{{ $movimiento->fechaCreacion }}</td>
									<td>{{ $movimiento->diferencia }}</td>
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

