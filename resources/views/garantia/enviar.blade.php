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
							<form role="form" method="POST" enctype="multipart/form-data" action="{{ URL::to('garantias/enviar') }}">
								{{ csrf_field() }}
								<!--Fecha Vencimiento-->
								<div class="form-group">
									<label for="estado" class="control-label">Estado</label>
									<select id="estado" class="form-control" name="estado" required autofocus>
										<option value="">Seleccione Estado</option>
										<option value="1">Cobro Documento</option>
										<option value="2">Devolución Documento</option>
									</select>
								</div>
								<!--Archivo-->
								<div class="form-group{{ $errors->has('archivo') ? ' has-error' : '' }}">
									<label for="archivo" class="control-label">Certificado de Conformidad</label>
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
										Documentos en formato PDF (hasta 10 MB)
									</span>
									@if ($errors->has('archivo'))
									<span class="help-block">
										<strong>{{ $errors->first('archivo') }}</strong>
									</span>
									@endif
								</div>
								<!--Observacion-->
								<div class="form-group">
									<label for="name" class="control-label">Observacion de Envio</label>
									<textarea class="form-control" rows="5" id="comment" id="observacion" name="observacion" maxlength="150" placeholder="Observación" autofocus required></textarea>
									<!--Elementos ocultos-->
									<input name="garantia_id" id="garantia_id" type="hidden" value="{{ $garantia->id }}">
									<input name="flujo" id="flujo" type="hidden" value="{{ $flujo }}">
								</div>
								<!--Boton de Envio-->
								<div class="form-group">
									<button type="submit" class="btn btn-primary">
										Enviar
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
<!--COMPLETA CAMPO DE ARCHIVO NAMEARCHIVO-->
<script>
$('input[name="archivo"]').change(function(){
	document.getElementById("nameArchivo").value = document.getElementById("archivo").value.replace(/\\/g, '/').replace(/.*\//, '');
});
</script>
@endsection

