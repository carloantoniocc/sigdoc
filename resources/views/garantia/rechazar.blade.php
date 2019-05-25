@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
			<!--Panel Formulario Crear Documento-->
			<div class="panel panel-default">
                <div class="panel-heading">Rechazar Documentos de Garantia</div>
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
									<th>Entidad Emisora</th>
								  </tr>
								</thead>
								<tbody>
								  <tr>
									<td>{{ $proveedor->rut }}-{{ $proveedor->dv }} | {{ $proveedor->name }}</td>
									<td>{{ $tipo->name }}</td>
									<td>{{ $garantia->nDoc }}</td>
									<td>{{ $garantia->fechaRecepcion }}</td>
									<td>{{ $banco->name }}</td>
								  </tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="row">	
						<div class="col-md-10 col-md-offset-1">
							<form role="form" method="POST" action="{{ URL::to('garantias/rechazar') }}">
								{{ csrf_field() }}
								<!--Campo Nombre-->
								<div class="form-group">
									<label for="name" class="control-label">Observacion de Rechazo</label>
									<textarea class="form-control" rows="5" id="comment" id="observacion" name="observacion" maxlength="150" placeholder="Observación" autofocus required></textarea>
									<!--Elementos ocultos-->
									<input name="garantia_id" id="garantia_id" type="hidden" value="{{ $garantia->id }}">
									<input name="flujo" id="flujo" type="hidden" value="{{ $flujo }}">
								</div>
								<div class="form-group">
									<button type="submit" class="btn btn-danger">
										Rechazar
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
@endsection

