@extends('layouts.app4')

@section('content')
<div class="container-fluid">
	<!--Mensajes de Guardado o Actualización de Documentos-->
	<?php $message=Session::get('message') ?>
	@if($message == 'store')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Documento Creado Exitosamente
		</div>
	@elseif($message == 'proveedor')
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Proveedor no Existe
		</div>
	@elseif($message == 'documento')
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Documento Existente
		</div>
	@endif
	<!--FIN Mensajes de Guardado o Actualización de Documentos-->
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
			<!--Panel Formulario Crear Documento-->
			<div class="panel panel-default">
                <div class="panel-heading">Ingresar Documentos</div>
                <div class="panel-body">
					
					<form class="form-horizontal" enctype="multipart/form-data" role="form" method="POST" action="{{ URL::to('oficinaPartes/store') }}">
						<!--Panel Izquierdo-->
						<div class="col-md-8">
							{{ csrf_field() }}
							<!--Proveedor-->
							<div class="form-group{{ $errors->has('proveedor') ? ' has-error' : '' }}">
								<label for="proveedor" class="col-md-4 control-label">Proveedor</label>
								<div class="col-md-8">
									<input id="proveedor" type="text" class="form-control" name="proveedor" maxlength="8" min="0" value="{{ old('proveedor') }}" required autofocus>
									@if ($errors->has('proveedor'))
									<span class="help-block">
										<strong>{{ $errors->first('proveedor') }}</strong>
									</span>
									@endif
								</div>
							</div>
							<!--Nro Documento-->
							<div class="form-group{{ $errors->has('nDoc') ? ' has-error' : '' }}">
								<label for="nDoc" class="col-md-4 control-label">Número de Documento</label>
								<div class="col-md-8">
									<input id="nDoc" type="number" class="form-control" name="nDoc" min="0" max="2147483646" value="{{ old('nDoc') }}" required autofocus>
									@if ($errors->has('nDoc'))
									<span class="help-block">
										<strong>{{ $errors->first('nDoc') }}</strong>
									</span>
									@endif
								</div>
							</div>
										
							<!--Tipo Documento-->
							<div class="form-group{{ $errors->has('tipo') ? ' has-error' : '' }}">
								<label for="tipo" class="col-md-4 control-label">Tipo de Documento</label>
								<div class="col-md-8">
									<select id="tipo" class="form-control" name="tipo" required>
										<option value="">Seleccione Tipo</option>
										@foreach($tipos as $tipo)
											@if( $tipo->id == old('tipo') )
												<option value="{{ $tipo->id }}-{{ $tipo->asociado }}" selected>{{ $tipo->name }}</option>
											@else
												<option value="{{ $tipo->id }}-{{ $tipo->asociado }}">{{ $tipo->name }}</option>
											@endif
										@endforeach
									</select>
									@if ($errors->has('tipo'))
									<span class="help-block">
										<strong>{{ $errors->first('tipo') }}</strong>
									</span>
									@endif
									<div id="alertaDocumento" class="alert alert-danger alert-dismissible" role="alert" style="display: none;">
										<button type="button" class="close" onclick="$('#alertaDocumento').hide()"><span aria-hidden="true">&times;</span></button>
										Documento Existente
									</div>
								</div>
							</div>
							
							<!--Factura Asociada-->
							<div class="form-group{{ $errors->has('facAsociada') ? ' has-error' : '' }}">
								<label for="facAsociada" class="col-md-4 control-label">Factura Asociada</label>
								<div class="col-md-8">
									<input id="facAsociada" type="number" class="form-control" name="facAsociada" min="0" max="2147483646" value="{{ old('facAsociada') }}" autofocus readonly>
									@if ($errors->has('facAsociada'))
									<span class="help-block">
										<strong>{{ $errors->first('facAsociada') }}</strong>
									</span>
									@endif
								</div>
							</div>

							<!--Fecha Recepcion-->
							<div class="form-group{{ $errors->has('fechaRecepcion') ? ' has-error' : '' }}">
								<label for="fechaRecepcion" class="col-md-4 control-label">Fecha de Recepción</label>
								<div class="col-md-8">
									<input type="text" class="form-control" name="fechaRecepcion" id="fechaRecepcion" value="{{ old('fechaRecepcion') }}" maxlength="10" placeholder="dd-mm-yyyy" required autofocus>
									@if ($errors->has('fechaRecepcion'))
									<span class="help-block">
										<strong>{{ $errors->first('fechaRecepcion') }}</strong>
									</span>
									@endif
								</div>
							</div>						
							
							<!--Fecha Emisión-->
							<div class="form-group{{ $errors->has('fechaEmision') ? ' has-error' : '' }}">
								<label for="fechaEmision" class="col-md-4 control-label">Fecha de Emisión</label>
								<div class="col-md-8">
									<input type="text" class="form-control" name="fechaEmision" id="fechaEmision" value="{{ old('fechaEmision') }}" maxlength="10" placeholder="dd-mm-yyyy" required autofocus>
									@if ($errors->has('fechaEmision'))
									<span class="help-block">
										<strong>{{ $errors->first('fechaEmision') }}</strong>
									</span>
									@endif
								</div>
							</div>
							
							<!--Fecha Vencimiento-->
							<div class="form-group{{ $errors->has('fechaVencimiento') ? ' has-error' : '' }}">
								<label for="fechaVencimiento" class="col-md-4 control-label">Fecha de Vencimiento</label>
								<div class="col-md-8">
									<input type="text" class="form-control" name="fechaVencimiento" id="fechaVencimiento" value="{{ old('fechaVencimiento') }}" maxlength="10" placeholder="dd-mm-yyyy" required autofocus>
									@if ($errors->has('fechaVencimiento'))
									<span class="help-block">
										<strong>{{ $errors->first('fechaVencimiento') }}</strong>
									</span>
									@endif
								</div>
							</div>
										
							<!--Monto-->
							<div class="form-group{{ $errors->has('monto') ? ' has-error' : '' }}">
								<label for="monto" class="col-md-4 control-label">Monto</label>
								<div class="col-md-8">
									<input id="monto" type="number" class="form-control" name="monto" min="0" max="2147483646" value="{{ old('monto') }}" required autofocus>
									@if ($errors->has('monto'))
									<span class="help-block">
										<strong>{{ $errors->first('monto') }}</strong>
									</span>
									@endif
								</div>
							</div>
										
							<!--Orden de Compra-->
							<div class="form-group{{ $errors->has('ordenCompra') ? ' has-error' : '' }}">
								<label for="ordenCompra" class="col-md-4 control-label">Orden de Compra</label>
								<div class="col-md-8">
									<input id="ordenCompra" type="text" class="form-control" name="ordenCompra" maxlength="15" value="{{ old('ordenCompra') }}" autofocus>
									@if ($errors->has('ordenCompra'))
									<span class="help-block">
										<strong>{{ $errors->first('ordenCompra') }}</strong>
									</span>
									@endif
								</div>
							</div>

							<!--Destino Documento-->
							<div class="form-group{{ $errors->has('establecimiento') ? ' has-error' : '' }}">
								<label for="establecimiento" class="col-md-4 control-label">Destino</label>
								<div class="col-md-8">
									<select id="establecimiento" class="form-control" name="establecimiento" required>
										<option value="establecimiento">Seleccione establecimiento</option>
										@foreach($establecimientos as $establecimiento)
												<option value="{{ $establecimiento->id }}">{{ $establecimiento->name }}</option>
										@endforeach
		
									</select>
								</div>
							</div>

		
							<!--Boton Submit-->
							<div class="form-group">
								<div class="col-md-6 col-md-offset-4">
									<input type="submit" name="send" id="send" value="Guardar" class="btn btn-primary"></input>									
								</div>
							</div>
						</div><!--Fin Div Panel Izquierdo -->
					
						<!--Panel Derecho-->
						<div class="col-md-4">
							<!--Boton para Crear Proveedor-->
							<div class="form-group">
								<div class="col-md-12">
									<a class="btn btn-default" href="{{ URL::to('proveedors') }}">
										Crear Proveedor
									</a>
								</div>	
							</div>

							<!--Adjuntar Archivo-->
							<div class="form-group{{ $errors->has('archivo') ? ' has-error' : '' }}">
								<div class="col-md-12">
									<div class="input-group">
										<label class="input-group-btn">
											<span class="btn btn-default">
												<img alt="" src="{{ asset('image/upload-box-solid.png') }}" style="heigth:16px; width:16px;">
												<input type="file" name="archivo" id="archivo" style="display: none;">
											</span>
										</label>
										<input type="text" class="form-control" name="nameArchivo" id="nameArchivo" placeholder="" readonly>
									</div>
									<span class="help-block">
										Documentos en formato PDF (hasta 10 MB)
									</span>
									@if ($errors->has('archivo'))
									<span class="help-block">
										<strong>{{ $errors->first('archivo') }}</strong>
									</span>
									@endif
								</div>
							</div>

																	
							<!--Observación-->
							<div class="form-group{{ $errors->has('observacion') ? ' has-error' : '' }}">
								<div class="col-md-12">
									<textarea class="form-control" rows="5" id="comment" id="observacion" name="observacion" maxlength="150" placeholder="Observación" autofocus>{{ old('observacion') }}</textarea>
									@if ($errors->has('observacion'))
									<span class="help-block">
										<strong>{{ $errors->first('observacion') }}</strong>
									</span>
									@endif
								</div>
							</div>		
						</div><!--Fin Div Panel Derecho---->
					</form>
				</div>
            </div>
			<!--FIN Panel Formulario Documento-->
        </div>
    </div>
</div>

<!--Script Tipo de Documento-->
<script>
document.getElementById('tipo').addEventListener('change', function() {
	var aux = this.value.split("-");	
	
	if (aux[1] == 1){
		document.getElementById("facAsociada").required = true;
		document.getElementById("facAsociada").readOnly = false; 
	}
	else {
		document.getElementById("facAsociada").required = false;
		document.getElementById("facAsociada").readOnly = true; 
		document.getElementById("facAsociada").value    = ""; 
	}
});	
</script>
<!--Script Calendario-->
<script>
$('#fechaRecepcion').datepicker({
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
document.getElementById('fechaRecepcion').addEventListener('keyup', function() {
	var v = this.value;
	if (v.match(/^\d{2}$/) !== null) {
		this.value = v + '-';
	} else if (v.match(/^\d{2}\-\d{2}$/) !== null) {
		this.value = v + '-';
	}
});		

$('#fechaEmision').datepicker({
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
});

//funcion que pone mascara de fecha
document.getElementById('fechaEmision').addEventListener('keyup', function() {
	var v = this.value;
	if (v.match(/^\d{2}$/) !== null) {
		this.value = v + '-';
	} else if (v.match(/^\d{2}\-\d{2}$/) !== null) {
		this.value = v + '-';
	}
});		

var fecha = new Date();
fecha.setDate(fecha.getDate() + 45); 

$('#fechaVencimiento').datepicker({
        dateFormat: "dd-mm-yy",
        firstDay: 1,
		minDate: 0, 
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
}).datepicker("setDate", fecha);

//funcion que pone mascara de fecha
document.getElementById('fechaVencimiento').addEventListener('keyup', function() {
	var v = this.value;
	if (v.match(/^\d{2}$/) !== null) {
		this.value = v + '-';
	} else if (v.match(/^\d{2}\-\d{2}$/) !== null) {
		this.value = v + '-';
	}
});	

</script>

<!-- AUTOCOMPLETA RUT -->
<script>
$("#proveedor").autocomplete({
	source: function(request, response) {
		$.ajax({
			url: "{{ route('getProveedor') }}",
			dataType: "json",
			data: {
				term : request.term
			},
			
			success: function(data) {
				response(data);
			}
		});
	},
	minLength: 2,
});

</script>

<!-- VERIFICA SI DOCUMENTO EXISTE -->
<script>
document.getElementById('tipo').addEventListener('change', function() {
	
	 var proveedor = document.getElementById('proveedor').value;
	 var nDoc      = document.getElementById('nDoc').value;
	
	$.get("getDocumento/"+proveedor+"/"+nDoc+"/"+event.target.value+"",function(response,state){
		if(response != 0){
			$('#alertaDocumento').show();
		}
	});
});	
</script>


<!--COMPLETA CAMPO DE ARCHIVO NAMEARCHIVO-->
<script>
$('input[name="archivo"]').change(function(){
	document.getElementById("nameArchivo").value = document.getElementById("archivo").value.replace(/\\/g, '/').replace(/.*\//, '');
});
</script>

<!--TRANSFORMA INPUT A ENTERO-->
<script>
$('#nDoc, #facAsociada, #monto').change(function(){
	document.getElementById(this.id).value = parseInt(this.value);
});
</script>


@endsection

