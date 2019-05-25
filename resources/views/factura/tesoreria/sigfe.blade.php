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
			<!--Panel Formulario Subir Documentos -->
			<div class="panel panel-default">
                <div class="panel-heading">Ingresar Archivo</div>
                <div class="panel-body">
					<br><br>
					<form class="form-horizontal" enctype="multipart/form-data" role="form" method="POST" action="{{ URL::to('tesoreria/uploadSigfe') }}">
						<div class="col-md-12">
							{{ csrf_field() }}
							<!--Adjuntar Archivo Facturas Recepcionadas por SIGFE-->
							<div class="form-group{{ $errors->has('archivo') ? ' has-error' : '' }}">
								<label for="archivo" class="col-md-3 control-label">Documentos Pagados</label>
								<div class="col-md-8">
									<div class="input-group">
										<label class="input-group-btn">
											<span class="btn btn-default">
												<img alt="" src="{{ asset('image/upload-box-solid.png') }}" style="heigth:16px; width:16px;">
												<input type="file" name="archivo" id="archivo" style="display: none;" accept="application/vnd.ms-excel" required>
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
<!--FIN COMPLETA CAMPO DE ARCHIVO NAMEARCHIVO-->
@endsection

