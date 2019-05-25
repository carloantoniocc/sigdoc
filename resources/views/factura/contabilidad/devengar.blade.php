@extends('layouts.app4')

@section('content')
<div class="container-fluid">
    <div class="row">
		<div class="col-lg-10 col-lg-offset-1 col-md-12">	
			<!--Panel Formulario Crear Documento-->
			<div class="panel panel-default">
                <div class="panel-heading">Devengar Documentos</div>
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
									<th>Monto</th>
								  </tr>
								</thead>
								<tbody>
								  <tr>
									<td>{{ $proveedor->rut }}-{{ $proveedor->dv }} | {{ $proveedor->name }}</td>
									<td>{{ $tipo->name }}</td>
									<td>{{ $documento->nDoc }}</td>
									<td>{{ $documento->fechaRecepcion }}</td>
									<td>{{ $documento->monto }}</td>
								  </tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="row">	
						<div class="col-md-10 col-md-offset-1">
							<form class="form-horizontal" role="form" method="POST" action="{{ URL::to('contabilidad/devengar') }}">
								{{ csrf_field() }}
								
								<div class="form-group{{ $errors->has('clasificador') ? ' has-error' : '' }}">
									<label for="clasificador" class="col-md-3 control-label">Item Presupuestario</label>
									<div class="col-md-6">
										<select id="clasificador" class="form-control" name="clasificador" required autofocus>
											<option value="">Seleccione Item Presupuestario</option>
											@foreach($clasificadores as $clasificador)
												@if( $clasificador->id == $documento->devengo_clasificador_id )
													<option value="{{ $clasificador->id }}" selected>{{ $clasificador->codigo }}-{{ $clasificador->name }}</option>
												@else
													<option value="{{ $clasificador->id }}">{{ $clasificador->codigo }}-{{ $clasificador->name }}</option>
												@endif
											@endforeach
										</select>
										@if ($errors->has('clasificador'))
										<span class="help-block">
											<strong>{{ $errors->first('clasificador') }}</strong>
										</span>
										@endif	
									</div>
								</div>
								<div class="form-group{{ $errors->has('fecha') ? ' has-error' : '' }}">
									<label for="fecha" class="col-md-3 control-label">Fecha de Devengo</label>
									<div class="col-md-6">
										<input type="text" class="form-control" name="fecha" id="fecha" maxlength="10" placeholder="dd-mm-yyyy" required autofocus>
										@if ($errors->has('fecha'))
										<span class="help-block">
											<strong>{{ $errors->first('fecha') }}</strong>
										</span>
										@endif
									</div>
								</div>
								<div class="form-group{{ $errors->has('observacion') ? ' has-error' : '' }}">
									<label for="observacion" class="col-md-3 control-label">Observación</label>
									<div class="col-md-6">
										<textarea class="form-control" rows="3" id="comment" id="observacion" name="observacion" maxlength="150" placeholder="Observación" autofocus>{{$documento->devengo_observacion}}</textarea>
										@if ($errors->has('observacion'))
										<span class="help-block">
											<strong>{{ $errors->first('observacion') }}</strong>
										</span>
										@endif
									</div>
								</div>
								<!--Elementos ocultos-->
								<input name="documento_id" id="documento_id" type="hidden" value="{{ $documento->id }}">
								<input name="searchRut" id="searchRut" type="hidden" value="{{ $searchRut }}">
								<input name="searchTipo" id="searchTipo" type="hidden" value="{{ $searchTipo }}">
								<input name="searchNdoc" id="searchNdoc" type="hidden" value="{{ $searchNdoc }}">
								<input name="searchEstado" id="searchEstado" type="hidden" value="{{ $searchEstado }}">
								<input name="page" id="page" type="hidden" value="{{ $page }}">
								
								<div class="form-group">
									<div class="col-md-7 col-md-offset-3">
										<button type="submit" class="btn btn-primary">
											Devengar
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
@if ($documento->devengo_fecha != null)
	var fecha = new Date('{{$documento->devengo_fecha}}'+'T12:00:00Z');
@else	
	var fecha = new Date();
@endif

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
}).datepicker("setDate", fecha);

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
@endsection

