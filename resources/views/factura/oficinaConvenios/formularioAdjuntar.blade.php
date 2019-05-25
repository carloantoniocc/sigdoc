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
									<th>Estado</th>
								  </tr>
								</thead>
								<tbody>
								  <tr>
									<td>{{ $proveedor->rut }}-{{ $proveedor->dv }} | {{ $proveedor->name }}</td>
									<td>{{ $tipo->name }}</td>
									<td>{{ $documento->nDoc }}</td>
									<td>{{ $documento->fechaRecepcion }}</td>
									<td>{{ $documento->ordenCompra }}</td>
									<td>
										@if( $movimientos->estado == 'DE' )
											En Contabilidad
										@elseif( $movimientos->estado == 'RC' )
											Documento Validado por Referente Técnico
										@elseif( $movimientos->estado == 'RT' )
											Jefe referente Tecnico - Por Validar
										@elseif( $movimientos->estado == 'VB' )
											Referente Tecnico - Por Validar			
										@elseif( $movimientos->estado == 'RE' )
											Rechazado
										@endif	

									</td>
								  </tr>
								</tbody>
							</table>
						</div>
					</div>























					<div class="row">	
						<div class="col-md-10 col-md-offset-1">
							<form role="form" enctype="multipart/form-data" method="POST" action="{{ URL::to('oficinaConvenios/adjuntar') }}">
								{{ csrf_field() }}
					<!-- ----------------------------------------------------------------------------------------------------------------------------------------- -->
					<!-- ----------------------------------------------------------------------------------------------------------------------------------------- -->
					@if( $movimientos->estado == 'RE' )
					<!-- ----------------------------------------------------------------------------------------------------------------------------------------- -->
					<!-- ----------------------------------------------------------------------------------------------------------------------------------------- -->
								<div class="row">
									<div class="col-md-8">
										<p><u><strong>DOCUMENTOS ADJUNTOS</strong></u></p> 
									</div>
								</div>
								@foreach($validadores as $validador)
									<div class="row">
										<div class="col-md-11 form-group{{ $errors->has('f'.$validador->validador_id) ? ' has-error' : '' }}">
											<label for="f{{ $validador->validador_id }}" class="col-md-3 control-label">{{ $validador->name }}</label>
											<div class="input-group col-md-9">
												<label class="input-group-btn">
													<!--
													<span class="btn btn-default">
														 <img alt="" src="{{ asset('image/upload-box-solid.png') }}" style="heigth:16px; width:16px;"> 
														 <input type="file" name="f{{ $validador->validador_id }}" id="f{{ $validador->validador_id }}" style="display: none;" onChange="archivos('archivo{{ $validador->validador_id }}',this.value)"> 
													</span>
													-->
												</label>
												<input type="text" class="form-control" name="archivo{{ $validador->validador_id }}" id="archivo{{ $validador->validador_id }}" value="{{ $validador->archivo }}" placeholder="">
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
					<!-- ----------------------------------------------------------------------------------------------------------------------------------------- -->
								<div class="row">
									<div class="col-md-8">
										<p><u><strong>AÑADIR OTRO DOCUMENTO</strong></u></p> 
									</div>
								</div>
								<div class="row">
									<div class="col-md-11 form-group{{ $errors->has('otro') ? ' has-error' : '' }}{{ $errors->has('tipo') ? ' has-error' : '' }}">
										<div class="col-md-3">
											<select id="tipo" class="form-control" name="tipo">
												<option value="48">NOTA DE CRÉDITO</option>
											</select>
										</div>
										<div class="input-group col-md-9">
											<label class="input-group-btn">
												<span class="btn btn-default">
													<img alt="" src="{{ asset('image/upload-box-solid.png') }}" style="heigth:16px; width:16px;">
													<input type="file" name="otro" id="otro" style="display: none;" onChange="archivos('archivo',this.value)">
												</span>
											</label>
											<input type="text" class="form-control" name="archivo" id="archivo" value="" placeholder="">
										</div>
										@if ($errors->has('otro'))
											<div class="input-group col-md-9 col-md-offset-3">
												<span class="help-block">
													<strong>Formato de Archivo Incorrecto</strong>
												</span>
											</div>
										@elseif ($errors->has('tipo'))
											<div class="input-group col-md-9 col-md-offset-3">
												<span class="help-block">
													<strong>Debe seleccionar un tipo de documento</strong>
												</span>
											</div>
										@endif										
									</div>
								</div>
								<br>
					<!-- ----------------------------------------------------------------------------------------------------------------------------------------- -->
					<!-- ----------------------------------------------------------------------------------------------------------------------------------------- -->
					@elseif( $movimientos->estado != 'RE' )
					<!-- ----------------------------------------------------------------------------------------------------------------------------------------- -->
					<!-- ----------------------------------------------------------------------------------------------------------------------------------------- -->										

								<div class="row">
									<div class="col-md-8">
										<p><u><strong>DOCUMENTOS ADJUNTOS</strong></u></p> 
									</div>
								</div>

									@foreach($validadores as $validador)
									<div class="row">
										<div class="col-md-11 form-group{{ $errors->has('f'.$validador->validador_id) ? ' has-error' : '' }}">
											<label for="f{{ $validador->validador_id }}" class="col-md-3 control-label">{{ $validador->name }}</label>
											<div class="input-group col-md-9">
												<label class="input-group-btn">
													<span class="btn btn-default">
														<img alt="" src="{{ asset('image/upload-box-solid.png') }}" style="heigth:16px; width:16px;">
														<input type="file" name="f{{ $validador->validador_id }}" id="f{{ $validador->validador_id }}" style="display: none;" onChange="archivos('archivo{{ $validador->validador_id }}',this.value)">
													</span>
												</label>
												<input type="text" class="form-control" name="archivo{{ $validador->validador_id }}" id="archivo{{ $validador->validador_id }}" value="{{ $validador->archivo }}" placeholder="">
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
					<!-- ----------------------------------------------------------------------------------------------------------------------------------------- -->							
								<div class="row">
									<div class="col-md-8">
										<p><u><strong>AÑADIR OTRO DOCUMENTO</strong></u></p> 
									</div>
								</div>
								<div class="row">
									<div class="col-md-11 form-group{{ $errors->has('otro') ? ' has-error' : '' }}{{ $errors->has('tipo') ? ' has-error' : '' }}">
										<div class="col-md-3">
											<select id="tipo" class="form-control" name="tipo">
												<option value="">Seleccione</option>
												@foreach($validadors as $tipo)
													<option value="{{ $tipo->id }}">{{ $tipo->name }}</option>
												@endforeach
											</select>
										</div>
										<div class="input-group col-md-9">
											<label class="input-group-btn">
												<span class="btn btn-default">
													<img alt="" src="{{ asset('image/upload-box-solid.png') }}" style="heigth:16px; width:16px;">
													<input type="file" name="otro" id="otro" style="display: none;" onChange="archivos('archivo',this.value)">
												</span>
											</label>
											<input type="text" class="form-control" name="archivo" id="archivo" value="" placeholder="">
										</div>
										@if ($errors->has('otro'))
											<div class="input-group col-md-9 col-md-offset-3">
												<span class="help-block">
													<strong>Formato de Archivo Incorrecto</strong>
												</span>
											</div>
										@elseif ($errors->has('tipo'))
											<div class="input-group col-md-9 col-md-offset-3">
												<span class="help-block">
													<strong>Debe seleccionar un tipo de documento</strong>
												</span>
											</div>
										@endif										
									</div>
								</div>
					<!-- ----------------------------------------------------------------------------------------------------------------------------------------- -->
					<!-- ----------------------------------------------------------------------------------------------------------------------------------------- -->					
					@endif	


								<br>
								<!--ELEMENTOS OCULTOS-->
								<input name="documento_id" id="documento_id" type="hidden" value="{{ $documento->id }}">
								
								<div class="row">
									<div class="form-group col-md-2">
										<input type="submit" name="send" id="send" value="Guardar" class="btn btn-primary"></input>
									</div>
									<div class="input-group col-md-8">
										<span class="help-block">
											Nota: Documentos en formato PDF (hasta 10 MB)
										</span>
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

<!--COMPLETA CAMPO DE ARCHIVO ARCHIVO(id)-->
<script>
function archivos(id,valor) {
	document.getElementById(id).value = valor.replace(/\\/g, '/').replace(/.*\//, '');
}
</script>
@endsection

