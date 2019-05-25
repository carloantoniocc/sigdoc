@extends('layouts.app4')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
			<!--Panel Formulario Crear Documento-->
			<div class="panel panel-default">
                <div class="panel-heading">Renueva Documentos de Garantia</div>
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
									<th>Fecha de Vencimiento</th>
									<th>Entidad Emisora</th>
								  </tr>
								</thead>
								<tbody>
								  <tr>
									<td>{{ $proveedor->rut }}-{{ $proveedor->dv }} | {{ $proveedor->name }}</td>
									<td>{{ $tipo->name }}</td>
									<td>{{ $garantia->nDoc }}</td>
									<td>{{ $mov->fechaVencimiento }}</td>
									<td>{{ $banco->name }}</td>
								  </tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="row">	
						<div class="col-md-10 col-md-offset-1">
							<form role="form" method="POST" action="{{ URL::to('garantias/renovar') }}">
								{{ csrf_field() }}
								<!--Fecha Vencimiento-->
								<div class="form-group">
									<label for="fechaVencimiento" class="control-label">Fecha de Renovación</label>
									<input type="text" class="form-control" name="fechaVencimiento" id="fechaVencimiento" maxlength="10" placeholder="dd-mm-yyyy" required autofocus>
								</div>
								<!--Campo Nombre-->
								<div class="form-group">
									<label for="name" class="control-label">Observacion de Renovación</label>
									<textarea class="form-control" rows="5" id="comment" id="observacion" name="observacion" maxlength="150" placeholder="Observación" autofocus required></textarea>
									<!--Elementos ocultos-->
									<input name="garantia_id" id="garantia_id" type="hidden" value="{{ $garantia->id }}">
									<input name="flujo" id="flujo" type="hidden" value="{{ $flujo }}">
								</div>
								<div class="form-group">
									<button type="submit" class="btn btn-primary">
										Renovar
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
<!--CALENDARIO FECHA DE VENCIMIENTO-->
<script>
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
@endsection

