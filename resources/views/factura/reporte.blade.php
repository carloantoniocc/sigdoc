@extends('layouts.app4')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
			<!--Panel Formulario Crear Contrarreferencia-->
			<div class="panel panel-default">
                <div class="panel-heading">Consulta Documentos</div>
                <div class="panel-body">
					{{ csrf_field() }}
					<form class="form-horizontal" role="form" method="GET" action="{{ URL::to('documentos/resultado') }}">
						
						<div class="form-group">
							<label for="proveedor" class="col-md-4 control-label">Proveedor</label>
							<div class="col-md-6">
								<input id="proveedor" type="text" class="form-control" name="proveedor" maxlength="10" min="0" autofocus>
                            </div>
						</div>
						
						<div class="form-group">
							<label for="nomDoc" class="col-md-4 control-label">Número de Documento</label>
							<div class="col-md-6">
								<input id="nomDoc" type="text" class="form-control" name="nomDoc" autofocus>
                            </div>
						</div>
						
						<div class="form-group">
							<label for="nomina" class="col-md-4 control-label">Nómina</label>
							<div class="col-md-6">
								<input id="nomina" type="text" class="form-control" name="nomina" autofocus>
                            </div>
						</div>
						
						<div class="form-group">
                            <label for="tipo" class="col-md-4 control-label">Tipo de Documento</label>
                            <div class="col-md-6">
                                <select id="tipo" class="form-control" name="tipo">
									<option value="">Seleccione</option>
									@foreach($tipos as $tipo)
										<option value="{{ $tipo->id }}">{{ $tipo->name }}</option>
									@endforeach
								</select>
                            </div>
                        </div>
						
						<div class="form-group">
                            <label for="monto" class="col-md-4 control-label">Monto</label>
                            <div class="col-md-6">
                                <input id="monto" type="number" class="form-control" name="monto" autofocus>
                            </div>
                        </div>

						<div class="form-group">
                            <label for="ordenCompra" class="col-md-4 control-label">Orden de Compra</label>
                            <div class="col-md-6">
                                <input id="ordenCompra" type="text" class="form-control" name="ordenCompra" autofocus>
                            </div>
                        </div>
						
						<div class="form-group">
                            <label for="referente" class="col-md-4 control-label">Departamento</label>
                            <div class="col-md-6">
                                <select id="referente" class="form-control" name="referente">
									<option value="">Seleccione</option>
									@foreach($referentes as $referente)
										<option value="{{ $referente->id }}">{{ $referente->name }}</option>
									@endforeach
								</select>
                            </div>
                        </div>
						
						<div class="form-group">
							<label for="fecha" class="col-md-4 control-label">Fecha Recepción ( desde / hasta )</label>
							<div class="col-md-3">
								<input id="desde" type="text" class="form-control" name="desde" placeholder="dd-mm-aaaa">
                            </div>
							<div class="col-md-3">
								<input id="hasta" type="text" class="form-control" name="hasta" placeholder="dd-mm-aaaa">
                            </div>
						</div>
						
						<div class="form-group">
                            <label for="estado" class="col-md-4 control-label">Estado</label>
                            <div class="col-md-6">
                                <select id="estado" class="form-control" name="estado">
									<option value="">Seleccione</option>
									<option value="OP">Ingresado Oficina de Partes</option>
									<option value="NP">Recepción Secretaría de Convenios</option>
									<option value="CV">Asignar a Referente Técnico</option>
									<option value="VB">Referente Técnico - Por Validar</option>
									<option value="RT">Jefe Referente Técnico - Por Validar</option>
									<option value="RC">Documento Validado por Referente Técnico</option>
									<option value="DE">En Contabilidad</option>
									<option value="TE">En Tesorería para Pago</option>
									<option value="EN">En Tesorería para Entrega</option>
									<option value="DV">Devuelto Referente Técnico a Convenios</option>
									<option value="DR">Devuelto Jefe R.T. a Referente Técnico</option>
									<option value="DO">Devuelto Convenios a Referente Técnico</option>
									<option value="DC">Devuelto Contabilidad a Convenios</option>
									<option value="FN">Entregado</option>
									<option value="RE">Rechazado</option>
								</select>
                            </div>
                        </div>
						
						<div class="form-group">
                            <label for="devengo" class="col-md-4 control-label">Devengo</label>
                            <div class="col-md-6">
                                <select id="devengo" class="form-control" name="devengo">
									<option value="">Seleccione</option>
									<option value="1">Si</option>
									<option value="0">No</option>
								</select>
                            </div>
                        </div>

							<!--Destino Documento-->
							
							<div class="form-group{{ $errors->has('establecimiento') ? ' has-error' : '' }}">
								<label for="establecimiento" class="col-md-4 control-label">Destino</label>
								<div class="col-md-6">
									<select id="establecimiento" class="form-control" name="establecimiento">
										<option value="">Seleccione establecimiento</option>
										@foreach($establecimientos as $establecimiento)
												<option value="{{ $establecimiento->id }}">{{ $establecimiento->name }}</option>
										@endforeach
		
									</select>
								</div>
							</div>
							
						<br>
						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Consultar
                                </button>
                            </div>
						</div>
					</form>
				</div>
            </div>
			<!--FIN Panel Formulario Documento-->
        </div>
    </div>
</div>
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

<!-- FECHA DESDE -->
<script>
$('#desde').datepicker({
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
document.getElementById('desde').addEventListener('keyup', function() {
	var v = this.value;
	if (v.match(/^\d{2}$/) !== null) {
		this.value = v + '-';
	} else if (v.match(/^\d{2}\-\d{2}$/) !== null) {
		this.value = v + '-';
	}
});
</script>
<!-- FECHA HASTA -->
<script>
$('#hasta').datepicker({
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
document.getElementById('hasta').addEventListener('keyup', function() {
	var v = this.value;
	if (v.match(/^\d{2}$/) !== null) {
		this.value = v + '-';
	} else if (v.match(/^\d{2}\-\d{2}$/) !== null) {
		this.value = v + '-';
	}
});
</script>
@endsection