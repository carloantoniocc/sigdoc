@extends('layouts.app4')

@section('content')
<div class="container-fluid">
	<!--Mensajes de Guardado o Actualización de Documentos-->
	<?php $message=Session::get('message') ?>
	@if($message == 'invalid')
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Documento Tiene un Formato Invalido
		</div>
	@elseif($message == 'extension')
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Documento debe ser en formato xls (Excel)
		</div>
	@elseif($message == 'archivo')
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Archivo con Formato Incorrecto
		</div>	
	@endif
	<!--FIN Mensajes de Guardado o Actualización de Documentos-->
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
			<!--Panel Formulario Subir Documentos Acepta-->
			<div class="panel panel-default">
                <div class="panel-heading">Ingresar Archivo Acepta</div>
                <div class="panel-body">
					<br><br>
					<form class="form-horizontal" enctype="multipart/form-data" role="form" method="POST" action="{{ URL::to('oficinaPartes/uploadAcepta') }}">
						<div class="col-md-12">
							{{ csrf_field() }}
							<!--Adjuntar Archivo Facturas Recepcionadas por ACEPTA-->
							<div class="form-group{{ $errors->has('archivo') ? ' has-error' : '' }}">
								<label for="archivo" class="col-md-3 control-label">Documentos Recepcionados por ACEPTA</label>
								<div class="col-md-8">
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
										Documentos en formato Excel (xls)
									</span>
									@if ($errors->has('archivo'))
									<span class="help-block">
										<strong>{{ $errors->first('archivo') }}</strong>
									</span>
									@endif
								</div>
							</div>
							<br>
							<!--Adjuntar Archivo Facturas NO Recepcionadas por ACEPTA-->
							<div class="form-group{{ $errors->has('archivo2') ? ' has-error' : '' }}">
								<label for="archivo2" class="col-md-3 control-label">Documentos NO Recepcionados por ACEPTA</label>
								<div class="col-md-8">
									<div class="input-group">
										<label class="input-group-btn">
											<span class="btn btn-default">
												<img alt="" src="{{ asset('image/upload-box-solid.png') }}" style="heigth:16px; width:16px;">
												<input type="file" name="archivo2" id="archivo2" style="display: none;">
											</span>
										</label>
										<input type="text" class="form-control" name="nameArchivo2" id="nameArchivo2" placeholder="" readonly>
									</div>
									<span class="help-block">
										Documentos en formato Excel (xls)
									</span>
									@if ($errors->has('archivo2'))
									<span class="help-block">
										<strong>{{ $errors->first('archivo2') }}</strong>
									</span>
									@endif
								</div>
							</div>
														
							<!--Boton Submit-->
							<div class="form-group">
								<div class="col-md-6 col-md-offset-3">
									<input type="submit" name="send" id="send" value="Subir" class="btn btn-primary"></input>									
								</div>
							</div>
						</div>
					</form>
				</div>
            </div>
			<!--FIN Panel Formulario Documento-->
        </div>
    </div>
</div>

<!--COMPLETA CAMPO DE ARCHIVO NAMEARCHIVO-->
<script>
	$('input[name="archivo"]').change(function(){
		document.getElementById("nameArchivo").value = document.getElementById("archivo").value.replace(/\\/g, '/').replace(/.*\//, '');
	});
</script>

<script>
$('input[name="archivo2"]').change(function(){
	document.getElementById("nameArchivo2").value = document.getElementById("archivo2").value.replace(/\\/g, '/').replace(/.*\//, '');
});
</script>

@endsection

