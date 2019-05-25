@extends('layouts.app4')

@section('content')
<div class="container-fluid">
	<!--Mensajes de Actualización de Documentos-->
	<?php $message=Session::get('message') ?>
	@if($message == 'pago')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Se Han Enviado el Documento a Entrega Exitosamente
		</div>
	@endif
	<!--FIN Mensajes de Actualización de Documentos-->
	
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Tesorería - Pago</div>
                <div class="panel-body">
					<!-- row Busquedas -->
					<div class="row">						
						<form class="form-horizontal" role="form" method="GET" action="{{ URL::to('tesoreria') }}">
							<!-- Filtro de Numero de Documento -->
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
					<form class="form-horizontal" role="form" method="POST" action="{{ URL::to('tesoreria/pago') }}">
						<div class="row">
							{{ csrf_field() }} 
							<div class="col-md-12">
								<table class="table table-striped">
									<thead>
									  <tr>
										<th><input type="checkbox" name="check_all" id="check_all"></th>
										<th>Proveedor</th>
										<th>Tipo de Documento</th>
										<th>Nro de Documento</th>
										<th>Monto</th>
										<th>Fecha de Recepcion</th>
										<th>Orden de Compra</th>
										<th>Departamento</th>
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
										<td>{{ $documento->ordenCompra }}</td>
										<td>{{ $documento->referente }}</td>
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
											<a href="{{ URL::to('tesoreria/' . $documento->id . '/validadores') }}" title="Revisar Validadores">Validadores</a> 
										</td>
									  </tr>
									  @endforeach
									</tbody>
								</table>
								<!--paginacion-->
								{{ $documentos->links() }}
							</div>
						</div><!-- FIN Lista de Documentos -->	
						<!--Boton que abre div de registro de pago-->
						<div class="row">	
							<div class="col-md-3">
								<a href="#pago" class="btn btn-primary btn-sm"  data-toggle="collapse" data-target="#pago">
									Registrar Pago
								</a>
							</div>
						</div>
						<!--Div de registro de pago-->
						<div class="row">
							</br>
							<div id="pago" class="col-md-12 collapse" style="border-top: 1px solid;border-color:#D3E0E9;">
								</br>
								<!--SIGFE-->
								<div class="form-group{{ $errors->has('sigfe') ? ' has-error' : '' }}">
									<label for="sigfe" class="col-md-3 control-label">Número de Folio SIGFE</label>
									<div class="col-md-6">
										<input id="sigfe" type="number" class="form-control" name="sigfe" min="0" max="2147483646" required autofocus>	
										@if ($errors->has('sigfe'))
										<span class="help-block">
											<strong>{{ $errors->first('sigfe') }}</strong>
										</span>
										@endif	
									</div>
								</div>
								<!--TIPO DE PAGO-->
								<div class="form-group{{ $errors->has('tipoPago') ? ' has-error' : '' }}">
									<label for="tipoPago" class="col-md-3 control-label">Tipo de Pago</label>
									<div class="col-md-6">
										<select id="tipoPago" class="form-control" name="tipoPago" required>
											<option value="">Seleccione</option>
											@foreach($tipoPagos as $tipoPago)
												<option value="{{ $tipoPago->id }}">{{ $tipoPago->name }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<!--CUENTA-->
								<div class="form-group{{ $errors->has('cuenta') ? ' has-error' : '' }}">
									<label for="cuenta" class="col-md-3 control-label">Cuenta</label>
									<div class="col-md-6">
										<select id="cuenta" class="form-control" name="cuenta">
											<option value="">Seleccione</option>
											@foreach($cuentas as $cuenta)
												<option value="{{ $cuenta->id }}">{{ $cuenta->name }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<!--FECHA PAGO-->
								<div class="form-group{{ $errors->has('fechaPago') ? ' has-error' : '' }}">
									<label for="fechaPago" class="col-md-3 control-label">Fecha de Pago</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="fechaPago" id="fechaPago" maxlength="10" placeholder="dd-mm-yyyy" required autofocus>
										@if ($errors->has('fechaPago'))
										<span class="help-block">
											<strong>{{ $errors->first('fechaPago') }}</strong>
										</span>
										@endif
									</div>
								</div>
								<!--OBSERVACION-->
								<div class="form-group">
									<label for="observacion" class="col-md-3 control-label">Observacion</label>
									<div class="col-md-6">
										<textarea class="form-control" rows="4" id="comment" id="observacion" name="observacion" maxlength="150" placeholder="Observación" autofocus></textarea>
									</div>	
								</div>
								<div class="form-group">
									<div class="col-md-7 col-md-offset-3">
										<button type="submit" class="btn btn-primary">
											Pagar
										</button>
									</div>
								</div>
							</div>
						</div>
					</form>
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
<!--Script Calendario-->
<script>
$('#fechaPago').datepicker({
        dateFormat: "dd-mm-yy",
        firstDay: 1,
        maxDate: 0,
		dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
        dayNamesShort: ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"],
        monthNames: 
            ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio",
            "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
        monthNamesShort: 
            ["Ene", "Feb", "Mar", "Abr", "May", "Jun",
            "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
		//focus
		onSelect: function ()
		{
			this.focus();
		}
}).datepicker("setDate", new Date());

//funcion que pone mascara de fecha
document.getElementById('fechaPago').addEventListener('keyup', function() {
	var v = this.value;
	if (v.match(/^\d{2}$/) !== null) {
		this.value = v + '-';
	} else if (v.match(/^\d{2}\-\d{2}$/) !== null) {
		this.value = v + '-';
	}
});
</script>
@endsection