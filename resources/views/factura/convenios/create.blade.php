@extends('layouts.app4')

@section('content')
<div class="container-fluid">
	<!--Mensajes de Guardado o Actualización de Documentos-->
	<?php $message=Session::get('message') ?>
	@if($message == 'proveedor')
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Proveedor no Existe
		</div>
	@endif
	<!--FIN Mensajes de Guardado o Actualización de Documentos-->
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
            <!--BreadCrumb-->
			<ol class="breadcrumb">
			  <li><a href="{{ URL::to('convenios') }}">Convenios</a></li>
			  <li class="active">Crear</li>
			</ol>
			<!--FIN BreadCrumb-->
			<!--Panel Formulario Crear Convenios-->
			<div class="panel panel-default">
                <div class="panel-heading">Crear Convenio</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ URL::to('convenios') }}">
                        {{ csrf_field() }}
						<!--Proveedor-->
						<div class="form-group{{ $errors->has('proveedor') ? ' has-error' : '' }}">
							<label for="proveedor" class="col-md-4 control-label">Proveedor</label>
							<div class="col-md-6">
								<input id="proveedor" type="text" class="form-control" name="proveedor" maxlength="8" min="0" value="{{ old('proveedor') }}" required autofocus>
								@if ($errors->has('proveedor'))
								<span class="help-block">
									<strong>{{ $errors->first('proveedor') }}</strong>
								</span>
								@endif
							</div>
						</div>
						
						<!--Tipo Compra-->
						<div class="form-group{{ $errors->has('tipo') ? ' has-error' : '' }}">
							<label for="tipo" class="col-md-4 control-label">Tipo de Compra</label>
							<div class="col-md-6">
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
						
						<!--Identificador-->
						<div class="form-group{{ $errors->has('identificador') ? ' has-error' : '' }}">
							<label for="identificador" class="col-md-4 control-label">Identificador</label>
							<div class="col-md-6">
								<input id="identificador" type="text" class="form-control" name="identificador" maxlength="150" value="{{ old('identificador') }}" required autofocus>
								@if ($errors->has('identificador'))
								<span class="help-block">
									<strong>{{ $errors->first('identificador') }}</strong>
								</span>
								@endif
							</div>
						</div>
						
						<!--Referente Técnico-->
						<div class="form-group{{ $errors->has('referente') ? ' has-error' : '' }}">
							<label for="referentes" class="col-md-4 control-label">Referente Técnico</label>
							<div class="col-md-6">
								<select id="referente" class="form-control" name="referente" required>
									<option value="">Seleccione Referente</option>
									@foreach($referentes as $referente)
										@if( $referente->id == old('referente') )
											<option value="{{ $referente->id }}" selected>{{ $referente->name }}</option>
										@else
											<option value="{{ $referente->id }}">{{ $referente->name }}</option>
										@endif
									@endforeach
								</select>
								@if ($errors->has('referente'))
								<span class="help-block">
									<strong>{{ $errors->first('referente') }}</strong>
								</span>
								@endif
							</div>
						</div>
						
						<!--Observación-->
						<div class="form-group{{ $errors->has('observacion') ? ' has-error' : '' }}">
							<label for="observacion" class="col-md-4 control-label">Observación</label>
							<div class="col-md-6">
								<input id="observacion" type="text" class="form-control" name="observacion" maxlength="150" value="{{ old('observacion') }}" required autofocus>
								@if ($errors->has('observacion'))
								<span class="help-block">
									<strong>{{ $errors->first('observacion') }}</strong>
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
                            	<input type="submit" name="send" id="send" value="Guardar" class="btn btn-primary"></input>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
			<!--FIN Panel Formulario Crear Comunas-->
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
@endsection

