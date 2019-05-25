@extends('layouts.app4')

@section('content')
<div class="container-fluid">
	<!--Mensajes de Guardado o Actualización de Convenios-->
	<?php $message=Session::get('message') ?>
	@if($message == 'proveedor')
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Proveedor no Existe
		</div>
	@endif
	<!--FIN Mensajes de Guardado o Actualización de Convenios-->
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
            <!--BreadCrumb-->
			<ol class="breadcrumb">
			  <li><a href="{{ URL::to('convenios') }}">Convenios</a></li>
			  <li class="active">Editar</li>
			</ol>
			<!--FIN BreadCrumb-->
			<!--Panel Formulario Crear Convenios-->
			<div class="panel panel-default">
                <div class="panel-heading">Editar Convenio</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ URL::to('convenios') }}/{{$convenio->id}}">
                        <input type="hidden" name="_method" value="PUT">
						{{ csrf_field() }}
						<!--Proveedor-->
						<div class="form-group">
							<label for="proveedor" class="col-md-4 control-label">Proveedor</label>
							<div class="col-md-6">
								<input id="proveedor" type="text" class="form-control" name="proveedor" value="{{$proveedor->rut}}-{{$proveedor->dv}} {{$proveedor->name}}" readonly>
							</div>
						</div>
						
						<!--Tipo Compra-->
						<div class="form-group{{ $errors->has('tipo') ? ' has-error' : '' }}">
							<label for="tipo" class="col-md-4 control-label">Tipo de Compra</label>
							<div class="col-md-6">
								<select id="tipo" class="form-control" name="tipo" required>
									<option value="">Seleccione Tipo</option>
									@foreach($tipos as $tipo)
										@if( $tipo->id == $convenio->tipoCompra_id )
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
						<div class="form-group">
							<label for="identificador" class="col-md-4 control-label">Identificador</label>
							<div class="col-md-6">
								<input id="identificador" type="text" class="form-control" name="identificador" value="{{ $convenio->identificador }}" readonly>
							</div>
						</div>
						
						<!--Referente Técnico-->
						<div class="form-group">
							<label for="referentes" class="col-md-4 control-label">Referente Técnico</label>
							<div class="col-md-6">
								<input id="referente" type="text" class="form-control" name="referente" value="{{$referente->name}}" readonly>
							</div>
						</div>
						
						<!--Observación-->
						<div class="form-group{{ $errors->has('observacion') ? ' has-error' : '' }}">
							<label for="observacion" class="col-md-4 control-label">Observación</label>
							<div class="col-md-6">
								<input id="observacion" type="text" class="form-control" name="observacion" maxlength="150" value="{{ $convenio->observacion }}" required autofocus>
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
								@if ($convenio->active == 1)
									<option value="1" selected>Si</option>
									<option value="0">No</option>
								@else
									<option value="1">Si</option>
									<option value="0" selected>No</option>		
								@endif
								</select>
                            </div>
                        </div>
						
						<!--Boton Submit-->
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
								<input type="submit" name="send" id="send" value="Editar" class="btn btn-primary"></input>                               
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

