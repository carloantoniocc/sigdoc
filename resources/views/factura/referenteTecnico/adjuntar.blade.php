@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
			<!--Panel Formulario Crear Documento-->
			<div class="panel panel-default">
                <div class="panel-heading">Adjuntar Documentos Validadores</div>
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
									<th>Fecha de Recepcion</th>
									<th>Orden de Compra</th>
								  </tr>
								</thead>
								<tbody>
								  <tr>
									<td>{{ $proveedor->rut }}-{{ $proveedor->dv }} | {{ $proveedor->name }}</td>
									<td>{{ $tipo->name }}</td>
									<td>{{ $documento->nDoc }}</td>
									<td>{{ $documento->fechaRecepcion }}</td>
									<td>{{ $documento->ordenCompra }}</td>
								  </tr>
								</tbody>
							</table>
						</div>
					</div>

					<div class="row">	
						<div class="col-md-10 col-md-offset-1">
							<form role="form" enctype="multipart/form-data" name="enviar" method="POST" action="{{ URL::to('referenteTecnico/adjuntar') }}">
								{{ csrf_field() }}
								@foreach($validadores as $validador)
									<div class="row">
										<div class="col-md-11 form-group{{ $errors->has('f'.$validador->validador_id) ? ' has-error' : '' }}">
											<label for="f{{ $validador->validador_id }}" class="col-md-3 control-label">{{ $validador->name }}</label>
											<div class="input-group col-md-9">
												<label class="input-group-btn">
													<span class="btn btn-default">
														<img alt="" src="{{ asset('image/upload-box-solid.png') }}" style="heigth:16px; width:16px;">
														<input type="file" name="f{{ $validador->validador_id }}" id="f{{ $validador->validador_id }}" style="display: none;" onChange="archivos('archivo{{ $validador->validador_id }}',this.value,2)">
													</span>
												</label>
												<input type="text" class="form-control" name="archivo{{ $validador->validador_id }}" id="archivo{{ $validador->validador_id }}" value="{{ $validador->archivo }}">
											</div>
											@if ($errors->has('f'.$validador->validador_id))
												<div class="input-group col-md-9 col-md-offset-3">
													<span class="help-block">
														<strong>Formato de Archivo Incorrecto</strong>
													</span>
												</div>
											@endif
										</div>
										
										<div class="col-md-1">	
											@if ( $validador->archivo != null )
												<a href="{{ asset($validador->archivo) }}" target="_blank">
													<span class="label label-success"> Adjunto   </span>
												</a>
											@endif
										</div>
									</div>
								@endforeach
								
								<div class="row">
									<div class="col-md-11 form-group">
										<div class="col-md-9 col-lg-offset-3"> 
											<span class="help-block">Nota: Documentos en formato PDF (hasta 10 MB)</span>
										</div>
									</div>
								</div>	
								
								<div class="row">
									<div class="col-md-12 form-group">
										<label for="glosaMemo" class="control-label">GLOSA MEMO AUTOMÁTICO <small class="text-muted">(opcional)</small></label>
										<textarea class="form-control" rows="6" id="comment" id="glosaMemo" name="glosaMemo" maxlength="1000" placeholder="Glosa Adicional en Memo Automático (opcional)"></textarea>
									</div>
								</div>
								
								<!--ELEMENTOS OCULTOS-->
								<input name="documento_id" id="documento_id" type="hidden" value="{{ $documento->id }}">
								<input name="adjuntar" id="adjuntar" type="hidden">
								
								<!--BOTON ENVIAR-->
								<div class="row">
									<div class="form-group col-md-2">
										<a class="btn btn-primary" href="#" onClick="funcionEnviar(1)">Enviar</a>
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
<!--COMPLETA CAMPO DE ARCHIVO ARCHIVO(id) Y ADJUNTA DOCUMENTO-->
<script>
function archivos(id,valor,action) {
	document.getElementById(id).value = valor.replace(/\\/g, '/').replace(/.*\//, '');
	funcionEnviar(action)
}
</script>

<!--EVENTO SUBMIT BOTON ENVIAR-->
<script>
function funcionEnviar(action) {
	document.getElementById("adjuntar").value = action;
	enviar.submit();
}
</script>
@endsection

