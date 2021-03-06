@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
			<!--Panel Formulario Crear Documento-->
			<div class="panel panel-default">
                <div class="panel-heading">Enviar Documentos</div>
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
							<form role="form" method="POST" action="{{ URL::to('oficinaConvenios/asignar') }}">
								{{ csrf_field() }}
								<!--Campo Nombre-->
								<div class="form-group">
									<div class="form-group">
										<label for="name" class="control-label">Seleccione Convenio</label>
										<select id="convenio" class="form-control" name="convenio" required>
											<option value="">Seleccione Convenio</option>
											@foreach($convenios as $convenio)
												<option value="{{ $convenio->id }}">{{ $convenio->identificador }} - {{ $convenio->referente_name }}</option>
											@endforeach
										</select>
									</div>	
									<!--Elementos ocultos-->
									<input name="documento_id" id="documento_id" type="hidden" value="{{ $documento->id }}">
									<input name="searchRut" id="searchRut" type="hidden" value="{{ $searchRut }}">
									<input name="searchTipo" id="searchTipo" type="hidden" value="{{ $searchTipo }}">
									<input name="searchNdoc" id="searchNdoc" type="hidden" value="{{ $searchNdoc }}">
									<input name="searchEstado" id="searchEstado" type="hidden" value="{{ $searchEstado }}">
									<input name="page" id="page" type="hidden" value="{{ $page }}">
								</div>
								</br></br>
								<div class="form-group">
									<input type="submit" name="send" id="send" value="Enviar" class="btn btn-primary"></input>
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

