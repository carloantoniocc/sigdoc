@extends('layouts.app4')

@section('content')
<div class="container-fluid">
	<!--Mensajes de Actualización de Documentos-->
	<?php $message=Session::get('message') ?>
	@if($message == 'entrega')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Se Ha Entregado el Documento Exitosamente
		</div>
	@endif
	<!--FIN Mensajes de Actualización de Documentos-->
	
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Tesorería - Entrega</div>
                <div class="panel-body">
					<!-- row Busquedas -->
					<div class="row">						
						<!-- Filtro de Numero de Documento -->
						<form class="form-horizontal" role="form" method="GET" action="{{ URL::to('tesoreria/entrega') }}">
							<div class="col-md-3">
								<div class="input-group">
									<input id="searchEgreso" name="searchEgreso" type="number" min="0" max="2147483646" class="form-control input-sm" placeholder="Buscar Número de Folio">
									<span class="input-group-btn ">
										<button class="btn btn-default btn-sm" type="submit">Ir</button>
									</span>
								</div>
							</div>
							<!-- Filtro de Numero de Documento -->
							<div class="col-md-3">
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
					<form class="form-horizontal" role="form" method="POST" action="{{ URL::to('tesoreria/entrega') }}">		
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
										<th>N° Folio SIGFE</th>
										<th>Tipo de Pago</th>
										<th>Fecha de Pago</th>
										<th>Observación</th>
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
										<td>{{ $documento->pago_sigfe }}</td>
										<td>{{ $documento->tipoPago }}</td>
										<td>{{ $documento->fechaPago }}</td>
										<td>
											@if( $documento->observacion != null )
												<a href="#" onClick="show('{{ $documento->observacion }}')">Ver</a>
											@else
												Sin Observación
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
						<!--Boton que abre div de registro de entrega-->
						<div class="row">	
							<div class="col-md-3">
								<a href="#entrega" class="btn btn-primary btn-sm"  data-toggle="collapse" data-target="#entrega">
									Registrar Entrega
								</a>
							</div>
						</div>
						<!--Div de registro de entrega-->
						<div class="row">
							</br>
							<div id="entrega" class="col-md-12 collapse" style="border-top: 1px solid;border-color:#D3E0E9;">
								</br>
								<!--RUT-->
								<div class="form-group{{ $errors->has('rut') ? ' has-error' : '' }}">
									<label for="rut" class="col-md-3 control-label">R.U.N. de quien retira</label>
									<div class="col-md-6">
										<input id="rut" type="text" class="form-control" name="rut" onchange="validaRut()" maxlength="10"  placeholder="12345678-9" required autofocus>	
										@if ($errors->has('rut'))
										<span class="help-block">
											<strong>{{ $errors->first('rut') }}</strong>
										</span>
										@endif	
									</div>
								</div>
								<!--NOMBRE-->
								<div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
									<label for="nombre" class="col-md-3 control-label">Nombre de quien retira</label>
									<div class="col-md-6">
										<input id="nombre" type="text" class="form-control" name="nombre" maxlength="150"  required autofocus>	
										@if ($errors->has('nombre'))
										<span class="help-block">
											<strong>{{ $errors->first('nombre') }}</strong>
										</span>
										@endif	
									</div>
								</div>
								<!--FECHA ENTREGA-->
								<div class="form-group{{ $errors->has('fechaEntrega') ? ' has-error' : '' }}">
									<label for="fechaEntrega" class="col-md-3 control-label">Fecha de Entrega</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="fechaEntrega" id="fechaEntrega" maxlength="10" placeholder="dd-mm-yyyy" required autofocus>
										@if ($errors->has('fechaEntrega'))
										<span class="help-block">
											<strong>{{ $errors->first('fechaEntrega') }}</strong>
										</span>
										@endif
									</div>
								</div>
								<div class="form-group">
									<div class="col-md-7 col-md-offset-3">
										<button type="submit" class="btn btn-primary">
											Entrega
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
<!-- FUNCION QUE MUESTRA LAS OBSERVACIONES DEL DOCUMENTO -->
<script>
function show (observacion) {
	$('#observacion').show();
	document.getElementById("obsTexto").innerHTML = observacion;
}
</script>
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
$('#fechaEntrega').datepicker({
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
document.getElementById('fechaEntrega').addEventListener('keyup', function() {
	var v = this.value;
	if (v.match(/^\d{2}$/) !== null) {
		this.value = v + '-';
	} else if (v.match(/^\d{2}\-\d{2}$/) !== null) {
		this.value = v + '-';
	}
});
</script>

<!--verificador RUT-->
<script>
function validaRut()
{
	var rut = document.getElementById("rut");
	rut.setCustomValidity("");
	
	var rexp = new RegExp(/^([0-9])+\-([kK0-9])+$/);
	var rutValue = rut.value;
	
	//elimina espacios y puntos
	rutValue = rutValue.replace(/\s+/g, '');
	rutValue = rutValue.replace(/\./g, '');

	if(rutValue.match(rexp)){
		//separa texto por guion
		
		var RUT = rutValue.split("-");
		var elRut = RUT[0]; 
		var factor = 2;
		var suma = 0;
		var dv;
		for(i=(elRut.length-1); i>=0; i--){
			factor = factor > 7 ? 2 : factor;
			suma += parseInt(elRut[i])*parseInt(factor++);
		}
		dv = 11 -(suma % 11);
		if(dv == 11){
			dv = 0;
		}
		else if (dv == 10){
			dv = "k";
		} 

		if(dv == RUT[1].toLowerCase()){
			document.getElementById("rut").value = RUT[0]+'-'+dv;
			//document.getElementById("dv").value = dv;
			return true;
		}
		else {         
			rut.setCustomValidity("El Rut es incorrecto");
			return false;
		}
	}
	else {
		rut.setCustomValidity("Formato Rut Incorrecto");
		return false;
	}
}
</script>
@endsection