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
                <div class="panel-heading">Ingresar Documento de Garantía</div>
                <div class="panel-body">
					<form class="form-horizontal" enctype="multipart/form-data" role="form" method="POST" action="{{ URL::to('garantias/oficinaPartes/store') }}">
						{{ csrf_field() }}
						<!--Div Panel Izquierdo---->
						<div class="col-md-8">
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
							
							<!--Tipo Documento-->
							<div class="form-group{{ $errors->has('tipo') ? ' has-error' : '' }}">
								<label for="tipo" class="col-md-4 control-label">Tipo de Documento</label>
								<div class="col-md-8">
									<select id="tipo" class="form-control" name="tipo" required>
										<option value="">Seleccione Tipo</option>
										@foreach($tipos as $tipo)
											@if( $tipo->id == old('tipo') )
												<option value="{{ $tipo->id }}" selected>{{ $tipo->name }}</option>
											@else
												<option value="{{ $tipo->id }}">{{ $tipo->name }}</option>
											@endif
										@endforeach
									</select>
									@if ($errors->has('tipo'))
									<span class="help-block">
										<strong>{{ $errors->first('tipo') }}</strong>
									</span>
									@endif
								</div>
							</div>
							
							<!--Banco-->
							<div class="form-group{{ $errors->has('banco') ? ' has-error' : '' }}">
								<label for="banco" class="col-md-4 control-label">Entidad Emisora</label>
								<div class="col-md-8">
									<select id="banco" class="form-control" name="banco" required>
										<option value="">Seleccione Banco</option>
										@foreach($bancos as $banco)
											@if( $banco->id == old('banco') )
												<option value="{{ $banco->id }}" selected>{{ $banco->name }}</option>
											@else
												<option value="{{ $banco->id }}">{{ $banco->name }}</option>
											@endif
										@endforeach
									</select>
									@if ($errors->has('banco'))
									<span class="help-block">
										<strong>{{ $errors->first('banco') }}</strong>
									</span>
									@endif
								</div>
							</div>
							
							<!--Nro Documento-->
							<div class="form-group{{ $errors->has('nDoc') ? ' has-error' : '' }}">
								<label for="nDoc" class="col-md-4 control-label">Número de Documento</label>
								<div class="col-md-8">
									<input id="nDoc" type="text" class="form-control" name="nDoc" min="0" maxlength="20" pattern="[0-9\s]+" value="{{ old('nDoc') }}" required autofocus>
									@if ($errors->has('nDoc'))
									<span class="help-block">
										<strong>{{ $errors->first('nDoc') }}</strong>
									</span>
									@endif
								</div>
							</div>
							
							<!--Moneda-->
							<div class="form-group{{ $errors->has('moneda') ? ' has-error' : '' }}">
								<label for="moneda" class="col-md-4 control-label">Moneda</label>
								<div class="col-md-8">
									<select id="moneda" class="form-control" name="moneda" required>
										<option value="">Seleccione Moneda</option>
										@foreach($monedas as $moneda)
											@if( $moneda->id == old('moneda') )
												<option value="{{ $moneda->id }}" selected>{{ $moneda->name }}</option>
											@else
												<option value="{{ $moneda->id }}">{{ $moneda->name }}</option>
											@endif
										@endforeach
									</select>
									@if ($errors->has('moneda'))
									<span class="help-block">
										<strong>{{ $errors->first('moneda') }}</strong>
									</span>
									@endif
								</div>
							</div>
							
							<!--Monto-->
							<div class="form-group{{ $errors->has('monto') ? ' has-error' : '' }}">
								<label for="monto" class="col-md-4 control-label">Monto</label>
								<div class="col-md-8">
									<input id="monto" type="number" step="0.0001" class="form-control" name="monto" min="0" max="9999999999" value="{{ old('monto') }}" required autofocus>
									@if ($errors->has('monto'))
									<span class="help-block">
										<strong>{{ $errors->first('monto') }}</strong>
									</span>
									@endif
								</div>
							</div>
							
							<!--Objeto de Garantía-->
							<div class="form-group{{ $errors->has('objeto') ? ' has-error' : '' }}">
								<label for="objeto" class="col-md-4 control-label">Objeto de Garantía</label>
								<div class="col-md-8">
									<select id="objeto" class="form-control" name="objeto" required>
										<option value="">Seleccione Objeto de Garantía</option>
										@foreach($objetos as $objeto)
											@if( $objeto->id == old('objeto') )
												<option value="{{ $objeto->id }}" selected>{{ $objeto->name }}</option>
											@else
												<option value="{{ $objeto->id }}">{{ $objeto->name }}</option>
											@endif
										@endforeach
									</select>
									@if ($errors->has('objeto'))
									<span class="help-block">
										<strong>{{ $errors->first('objeto') }}</strong>
									</span>
									@endif
								</div>
							</div>
							
							<!--Licitacion / Motivo-->
							<div class="form-group{{ $errors->has('licitacion') ? ' has-error' : '' }}">
								<label for="licitacion" class="col-md-4 control-label">Licitación / Motivo</label>
								<div class="col-md-8">
									<input id="licitacion" type="text" class="form-control" name="licitacion" maxlength="150" value="{{ old('licitacion') }}" required autofocus>
									@if ($errors->has('licitacion'))
									<span class="help-block">
										<strong>{{ $errors->first('licitacion') }}</strong>
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
							
							<div class="form-group">
								<div class="col-md-6 col-md-offset-4">
									<button type="submit" class="btn btn-primary">
										Guardar
									</button>
								</div>
							</div>
						</div>
						<!--Fin Div Panel Izquierdo---->
						
						<!--Div Panel Derecho---->
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
						</div>
						<!--Fin Div Panel Derecho---->	
					</form>
				</div>
			</div>	
		</div>
	</div>
</div>
<!-- AUTOCOMPLETA RUT -->
<script>
$("#proveedor").autocomplete({
	source: function(request, response) {
		$.ajax({
			url: "{{ route('getProveedorGarantia') }}",
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
});

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

<!--COMPLETA CAMPO DE ARCHIVO NAMEARCHIVO-->
<script>
$('input[name="archivo"]').change(function(){
	document.getElementById("nameArchivo").value = document.getElementById("archivo").value.replace(/\\/g, '/').replace(/.*\//, '');
});
</script>
@endsection