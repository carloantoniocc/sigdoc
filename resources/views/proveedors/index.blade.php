@extends('layouts.app')

@section('content')
<div class="container-fluid">
	<!--Mensajes de Guardado o Actualización de Proveedores-->
	<?php $message=Session::get('message') ?>
	@if($message == 'store')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Proveedor Creado Exitosamente
		</div>
	@elseif($message == 'update')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Proveedor Modificado Exitosamente
		</div>
	@endif
	<!--FIN Mensajes de Guardado o Actualización de Proovedores-->
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Lista de Proveedores</div>
                <div class="panel-body">
                    {{ csrf_field() }} 
					<div class="row">
						<!-- Boton Crear Nuevo Proveedor -->
						<div class="col-md-2">
							<a class="btn btn-sm btn-primary" href="{{ URL::to('proveedors/create') }}">Crear Proveedor</a>
						</div>
						<!-- Formulario de Filtro por Nombre -->
						<div class="col-md-5">
							<form class="form-horizontal" role="form" method="GET" action="{{ URL::to('proveedors') }}">
								<div class="input-group">
									<input id="searchNombre" name="searchNombre" type="text" class="form-control input-sm" maxlength="150" placeholder="Buscar por Nombre">
									<span class="input-group-btn ">
										<button class="btn btn-default btn-sm" type="submit">Ir</button>
									</span>
								</div>
							</form>
						</div>
						<!-- Formulario de Filtro por Rut -->
						<div class="col-md-5">
							<form class="form-horizontal" role="form" method="GET" action="{{ URL::to('proveedors') }}">
								<div class="input-group">
									<input id="searchRut" name="searchRut" type="text" class="form-control input-sm" maxlength="8" placeholder="Buscar por RUT (sin puntos ni dígito verificador)">
									<span class="input-group-btn ">
										<button class="btn btn-default btn-sm" type="submit">Ir</button>
									</span>
								</div>
							</form>
						</div>
					</div>
					</br>
					<!-- Lista de Proveedores -->		
					<div class="row">
						<div class="col-md-12">
							<table class="table table-striped">
								<thead>
								  <tr>
									<th>Nombre</th>
									<th>Rut</th>
									<th>Estado</th>
									<th>Editar</th>
								  </tr>
								</thead>
								<tbody>
								  @foreach($proveedors as $proveedor)
								  <tr>
									<td>{{ $proveedor->name }}</td>
									<td>{{ $proveedor->rut }} - {{ $proveedor->dv }}</td>
									<td>
										@if( $proveedor->active == 1 )
											Activo
										@else
											Inactivo
										@endif
									</td>
									<td><a href="{{ URL::to('proveedors/' . $proveedor->id . '/edit') }}">Editar</a></td>
								  </tr>
								  @endforeach
								</tbody>
							</table>
							<!--paginacion-->
							{{ $proveedors->links() }}
						</div>
					</div>
					<!-- FIN Lista de Proveedores -->			
                </div>
            </div>
        </div>
    </div>
</div>
@endsection