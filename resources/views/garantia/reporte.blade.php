@extends('layouts.app4')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
			<!--Panel Formulario Crear Contrarreferencia-->
			<div class="panel panel-default">
                <div class="panel-heading">Consulta Documentos de Garantía</div>
                <div class="panel-body">
					{{ csrf_field() }}
					<form class="form-horizontal" role="form" method="GET" action="{{ URL::to('garantias/resultado') }}">
						
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
							<label for="objeto" class="col-md-4 control-label">Objeto de Garantía</label>
							<div class="col-md-6">
								<select id="objeto" class="form-control" name="objeto">
									<option value="">Seleccione</option>
									@foreach($objetos as $objeto)
										<option value="{{ $objeto->id }}">{{ $objeto->name }}</option>
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
									<option value="NP">Recepción Convenios / Abastecimiento</option>
									<option value="RN">Renovado Convenios / Abastecimiento</option>
									<option value="TE">En Tesorería</option>
									<option value="DG">Devuelto a Proveedor</option>
									<option value="CG">Cobrado</option>
									<option value="RE">Rechazado</option>
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