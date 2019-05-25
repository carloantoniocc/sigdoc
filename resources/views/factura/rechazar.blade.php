@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
			<!--Panel Formulario Crear Documento-->
			<div class="panel panel-default">
                <div class="panel-heading">Rechazar Documentos</div>
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
							<form role="form" method="POST" action="{{ URL::to('documentos/rechazar') }}">
								{{ csrf_field() }}
								<!--Campo Nombre-->
								<div class="form-group">
									<label for="name" class="control-label">Observacion de Rechazo</label>
									<textarea class="form-control" rows="5" id="comment" id="observacion" name="observacion" maxlength="150" placeholder="Observación" autofocus required></textarea>
									<!--Elementos ocultos-->
									<input name="documento_id" id="documento_id" type="hidden" value="{{ $documento->id }}">
									<input name="flujo" id="flujo" type="hidden" value="{{ $flujo }}">
								</div>
								<div class="form-group">
									<input type="submit" name="send" id="send" value="Rechazar" class="btn btn-danger"></input>
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
@endsection

