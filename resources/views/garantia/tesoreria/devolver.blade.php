@extends('layouts.app4')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
			<!--Panel Formulario Crear Documento-->
			<div class="panel panel-default">
                <div class="panel-heading">Devolución de Documentos de Garantia</div>
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
							<form class="form-horizontal" role="form" method="POST" action="{{ URL::to('garantias/devolver') }}">
								{{ csrf_field() }}
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
								<!--FECHA-->
								<div class="form-group{{ $errors->has('fechaEntrega') ? ' has-error' : '' }}">
									<label for="fecha" class="col-md-3 control-label">Fecha de autorización</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="fecha" id="fecha" maxlength="10" placeholder="dd-mm-yyyy" required autofocus>
										@if ($errors->has('fecha'))
										<span class="help-block">
											<strong>{{ $errors->first('fecha') }}</strong>
										</span>
										@endif
									</div>
								</div>
								<input name="garantia_id" id="garantia_id" type="hidden" value="{{ $garantia->id }}">
								
								<!--Boton de Envio-->
								<div class="form-group">
									<div class="col-md-7 col-md-offset-3">
										<button type="submit" class="btn btn-primary">
											Registrar Devolución
										</button>
									</div>
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
<!--Script Calendario-->
<script>
$('#fecha').datepicker({
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
document.getElementById('fecha').addEventListener('keyup', function() {
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

