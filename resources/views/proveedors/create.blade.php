@extends('layouts.app')

@section('content')
<div class="container-fluid">
	<!--Mensajes de Verificacion de Rut Proveedores-->
	<?php $message=Session::get('message') ?>
	@if($message == 'rut')
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			R.U.T. ya ha sido asignado a otro proveedor
		</div>
	@endif
	<!--FIN Mensajes de Verificacion de Rut Proveedores-->
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
            <!--BreadCrumb-->
			<ol class="breadcrumb">
			  <li><a href="{{ URL::to('proveedors') }}">Proveedores</a></li>
			  <li class="active">Crear</li>
			</ol>
			<!--FIN BreadCrumb-->
			<!--Panel Formulario Crear Proveedor-->
			<div class="panel panel-default">
                <div class="panel-heading">Crear Proveedor</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ URL::to('proveedors') }}">
                        {{ csrf_field() }}
						<!--Campo Rut-->
                        <div class="form-group{{ $errors->has('run') ? ' has-error' : '' }}">
                            <label for="run" class="col-md-4 control-label">R.U.T.</label>

                            <div class="col-md-6">
                                <input id="run" type="text" class="form-control" name="run" value="{{ old('run') }}" onchange="validaRut()" maxlength="10" required autofocus>

                                @if ($errors->has('run'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('run') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						<!--Elementos ocultos-->
						<input name="rut" id="rut" type="hidden" value="{{ old('rut') }}">
						<input name="dv" id="dv" type="hidden" value="{{ old('dv') }}">
						
						<!--Campo Nombre-->
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Nombre</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" maxlength="150" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						
						<!--Campo Fantasia-->
                        <div class="form-group{{ $errors->has('fantasia') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Nombre de Fantasía</label>

                            <div class="col-md-6">
                                <input id="fantasia" type="text" class="form-control" name="fantasia" value="{{ old('fantasia') }}" maxlength="150" autofocus>

                                @if ($errors->has('fantasia'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fantasia') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

						<!--Lista Comuna-->
						<div class="form-group{{ $errors->has('comuna') ? ' has-error' : '' }}">
                            <label for="comuna" class="col-md-4 control-label">Comuna</label>

                            <div class="col-md-6">
								<select id="comuna" class="form-control" name="comuna" required>
								  <option value="">Seleccione Comuna</option>
								  @foreach($comunas as $comuna)
										@if($comuna->id == old('comuna'))
											<option value="{{ $comuna->id }}" selected>{{ $comuna->name }}</option>
										@else
											<option value="{{ $comuna->id }}">{{ $comuna->name }}</option>
										@endif
								  @endforeach
								</select>
                            </div>
                        </div>
						<!--Campo Direccion-->
                        <div class="form-group{{ $errors->has('direccion') ? ' has-error' : '' }}">
                            <label for="direccion" class="col-md-4 control-label">Dirección</label>

                            <div class="col-md-6">
                                <input id="direccion" type="text" class="form-control" name="direccion" value="{{ old('direccion') }}" maxlength="150" onFocus="geolocate()" required autofocus>

                                @if ($errors->has('direccion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('direccion') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						<!--Elementos ocultos-->
						<input name="x" id="x" type="hidden" value="{{ old('x') }}">
						<input name="y" id="y" type="hidden" value="{{ old('y') }}">
						<!--Campo Telefono-->
                        <div class="form-group{{ $errors->has('telefono') ? ' has-error' : '' }}">
                            <label for="telefono" class="col-md-4 control-label">Teléfono</label>

                            <div class="col-md-6">
                                <input id="telefono" type="text" class="form-control" name="telefono" value="{{ old('telefono') }}" maxlength="11" pattern="[0-9]+" placeholder="sólo numeros" required autofocus>

                                @if ($errors->has('telefono'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						<!--Campo Email-->
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">Correo Electrónico</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" maxlength="150" autofocus>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						<!--Lista Activo-->
						<div class="form-group{{ $errors->has('active') ? ' has-error' : '' }}">
                            <label for="active" class="col-md-4 control-label">Activo</label>

                            <div class="col-md-6">
								<select id="active" class="form-control" name="active" required>
								  <option value="1">Si</option>
								  <option value="0">No</option>
								</select>
                            </div>
                        </div>
						
						<!--Boton Submit-->
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
			<!--FIN Panel Formulario Crear Proveedores-->
        </div>
    </div>
</div>

<!--verificador RUT-->
<script>
function validaRut()
{
	var rut = document.getElementById("run");
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
			document.getElementById("rut").value = RUT[0];
			document.getElementById("dv").value = dv;
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

<!--evita submit al presionar enter-->
<script>
	
	function stopRKey(evt) {
		var evt = (evt) ? evt : ((event) ? event : null);
		var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
		if ((evt.keyCode == 13) && (node.type=="text")) {return false;}
	}

	document.onkeypress = stopRKey;
</script>

<!--Script Autocompletado-->
<script>
	var placeSearch, autocomplete;
	
	function initAutocomplete() {
		
		var options = {
				componentRestrictions: {
				country: 'cl'
			}
		};
		
		autocomplete = new google.maps.places.Autocomplete(
			(document.getElementById('direccion')),
			options,
			{types: ['geocode']}			
		);
		
		autocomplete.addListener('place_changed', fillInAddress);
	}
	
	function fillInAddress() {
		document.getElementById("y").value = autocomplete.getPlace().geometry.location.lat();
		document.getElementById("x").value = autocomplete.getPlace().geometry.location.lng();
	}
	
	function geolocate() {
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(position) {
            var geolocation = {
              lat: position.coords.latitude,
              lng: position.coords.longitude
            };
            var circle = new google.maps.Circle({
              center: geolocation,
              radius: position.coords.accuracy
            });
            autocomplete.setBounds(circle.getBounds());
          });
        }
    }
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAlnRCAhCvNWeH-8P-fGEnoQJ2Hi0nZv-Y&libraries=places&callback=initAutocomplete"
         async defer></script>
@endsection

