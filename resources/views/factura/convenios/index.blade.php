@extends('layouts.app')

@section('content')
<div class="container-fluid">
	<!--Mensajes de Actualización de Convenios-->
	<?php $message=Session::get('message') ?>
	@if($message == 'store')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Convenio Creado Exitosamente
		</div>
	@elseif($message == 'update')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Convenio Modificado Exitosamente
		</div>	
	@elseif($message == 'validadores')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Se Han Asignado los Validadores Exitosamente
		</div>		
	@endif
	<!--FIN Mensajes de Actualización de Convenios-->
	
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Gestión de Convenios</div>
                <div class="panel-body">
					{{ csrf_field() }} 
					<!-- row Busquedas -->
					<div class="row">						
						<!-- Boton Crear Convenios-->
						<div class="col-md-4">
							<a class="btn btn-sm btn-primary" href="{{ URL::to('convenios/create') }}">Crear Convenio</a>
						</div>

						<!-- Filtro de Rut -->
						<div class="col-md-4">
							<form class="form-horizontal" role="form" method="GET" action="{{ URL::to('convenios') }}">
								<div class="input-group">
									<input id="searchRut" name="searchRut" type="text" class="form-control input-sm" maxlength="8" placeholder="Buscar RUT (sin puntos ni dígito verificador)">
									<span class="input-group-btn ">
										<button class="btn btn-default btn-sm" type="submit">Ir</button>
									</span>
								</div>
							</form>
						</div>
						<!-- Filtro por Id de Convenio -->
						<div class="col-md-4">
							<form class="form-horizontal" role="form" method="GET" action="{{ URL::to('convenios') }}">
								<div class="input-group">
									<input id="searchId" name="searchId" type="text" class="form-control input-sm" maxlength="100" placeholder="Buscar por Identificador">
									<span class="input-group-btn ">
										<button class="btn btn-default btn-sm" type="submit">Ir</button>
									</span>
								</div>
							</form>
						</div>	
					</div>
					<!-- FIN row Busquedas -->
					</br> 
					<!-- Lista de Convenios -->		
					<div class="row">
						<div class="col-md-12">
							<table class="table table-striped">
								<thead>
									<tr>
										<th>Proveedor</th>
										<th>Tipo de Compra</th>
										<th>Identificador</th>
										<th>Referente Técnico</th>
										<th>Observación</th>
										<th>Observación</th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody>
									@foreach($convenios as $convenio)
										<tr>
											<td>{{ $convenio->rut }} | {{ $convenio->nameProveedor }}</td>
											<td>{{ $convenio->nameTipoCompras }}</td>
											<td>{{ $convenio->identificador }}</td>
											<td>{{ $convenio->nameReferente }}</td>
											<td>{{ $convenio->observacion }}</td>
											<td>
												@if( $convenio->active == 1 )
													Activo
												@else
													Inactivo
												@endif
											</td>
											<td>
												<a href="{{ URL::to('convenios/validadores/' . $convenio->id) }}">Validadores</a> | 
												<a href="{{ URL::to('convenios/' . $convenio->id . '/edit') }}">Editar</a>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
							<!--paginacion-->
							{{ $convenios->links() }}
						</div>
					</div>
					<!-- FIN Lista de Convenios -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection