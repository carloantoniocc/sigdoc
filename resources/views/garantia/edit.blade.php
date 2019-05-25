@extends('layouts.app4')

@section('content')
<div class="container-fluid">
	<!--Mensajes de Guardado o Actualización de Documentos-->
	<?php $message=Session::get('message') ?>
	@if($message == 'documento')
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
                <div class="panel-heading">Editar Documento de Garantía</div>
                <div class="panel-body">
					<form class="form-horizontal" enctype="multipart/form-data" role="form" method="POST" action="{{ URL::to('garantias') }}/{{$garantia->id}}">
						<input type="hidden" name="_method" value="PUT">
						{{ csrf_field() }}
						<!--Div Panel Izquierdo---->
						<div class="col-md-8">
							<!--Proveedor-->
							<div class="form-group">
								<label for="proveedor" class="col-md-4 control-label">Proveedor</label>
								<div class="col-md-8">
									<input id="proveedor" type="text" class="form-control" name="proveedor" maxlength="10" min="0" value="{{$proveedor->rut}}-{{$proveedor->dv}} {{$proveedor->name}}" readonly>
								</div>
							</div>
							<!--Elementos ocultos-->
							<input name="proveedor_id" id="proveedor_id" type="hidden" value="{{$garantia->proveedor_id}}">
							<input name="flujo" id="flujo" type="hidden" value="{{$flujo}}">
							
							<!--Tipo Documento-->
							<div class="form-group{{ $errors->has('tipo') ? ' has-error' : '' }}">
								<label for="tipo" class="col-md-4 control-label">Tipo de Documento</label>
								<div class="col-md-8">
									<select id="tipo" class="form-control" name="tipo" required>
										<option value="">Seleccione Tipo</option>
										@foreach($tipos as $tipo)
											@if( $tipo->id == $garantia->tipoDoc_id )
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
											@if( $banco->id == $garantia->banco_id )
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
									<input id="nDoc" type="text" class="form-control" name="nDoc" min="0" maxlength="20" pattern="[0-9\s]+" value="{{ $garantia->nDoc }}" required autofocus>
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
											@if( $moneda->id == $garantia->moneda_id )
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
									<input id="monto" type="number" step="0.0001" class="form-control" name="monto" min="0" max="9999999999" value="{{ $garantia->monto }}" required autofocus>
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
											@if( $objeto->id == $garantia->objeto_garantia_id )
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
									<input id="licitacion" type="text" class="form-control" name="licitacion" maxlength="150" value="{{ $garantia->licitacion }}" required autofocus>
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
									<input type="text" class="form-control" name="fechaRecepcion" id="fechaRecepcion" value="{{ $garantia->fechaRecepcion }}" maxlength="10" placeholder="dd-mm-yyyy" required autofocus>
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
									<input type="text" class="form-control" name="fechaEmision" id="fechaEmision" value="{{ $garantia->fechaEmision }}" maxlength="10" placeholder="dd-mm-yyyy" required autofocus>
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
									<input type="text" class="form-control" name="fechaVencimiento" id="fechaVencimiento" value="{{ $garantia->fechaVencimiento }}" maxlength="10" placeholder="dd-mm-yyyy" required autofocus>
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
										Editar
									</button>
								</div>
							</div>
						</div>
						<!--Fin Div Panel Izquierdo---->
						
						<!--Div Panel Derecho---->
						<div class="col-md-4">
							<!--Adjuntar Archivo-->
							<div class="form-group{{ $errors->has('archivo') ? ' has-error' : '' }}">
								<div class="col-md-12">
									<div class="input-group">
										<label class="input-group-btn">
											<span class="btn btn-default">
												<img alt="" src="{{ asset('image/upload-box-solid.png') }}" style="heigth:16px; width:16px;">
												<input type="file" name="archivo" id="archivo" value="{{ $garantia->archivo }}" style="display: none;">
											</span>
										</label>
										<input type="text" class="form-control" name="nameArchivo" id="nameArchivo" value="{{ $garantia->archivo }}" placeholder="" readonly>
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
						</div>
						<!--Fin Div Panel Derecho---->	
					</form>
				</div>
			</div>	
		</div>
	</div>
</div>
<!--Script Calendario-->
<script>
var fecha1 = new Date('{{$garantia->fechaRecepcion}}'+'T12:00:00Z');

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
}).datepicker("setDate", fecha1);

//funcion que pone mascara de fecha
document.getElementById('fechaRecepcion').addEventListener('keyup', function() {
	var v = this.value;
	if (v.match(/^\d{2}$/) !== null) {
		this.value = v + '-';
	} else if (v.match(/^\d{2}\-\d{2}$/) !== null) {
		this.value = v + '-';
	}
});	

var fecha2 = new Date('{{$garantia->fechaEmision}}'+'T12:00:00Z');

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
}).datepicker("setDate", fecha2);

//funcion que pone mascara de fecha
document.getElementById('fechaEmision').addEventListener('keyup', function() {
	var v = this.value;
	if (v.match(/^\d{2}$/) !== null) {
		this.value = v + '-';
	} else if (v.match(/^\d{2}\-\d{2}$/) !== null) {
		this.value = v + '-';
	}
});	

var fecha3 = new Date('{{$garantia->fechaVencimiento}}'+'T12:00:00Z');

$('#fechaVencimiento').datepicker({
        dateFormat: "dd-mm-yy",
        firstDay: 1,
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
}).datepicker("setDate", fecha3);

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