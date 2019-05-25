@extends('layouts.app')

@section('content')
<div class="container-fluid">
	<!--Mensajes de Guardado o Actualización de Usuarios-->
	<?php $message=Session::get('message') ?>
	@if($message == 'store')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Usuario Creado Exitosamente
		</div>
	@elseif($message == 'update')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Usuario Modificado Exitosamente
		</div>
	@elseif($message == 'roles')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Roles Asignados Exitosamente
		</div>
	@elseif($message == 'establecimientos')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Establecimientos Asignados Exitosamente
		</div>
	@elseif($message == 'referentes')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Departamentos Asignados Exitosamente
		</div>	
	@elseif($message == 'error_referentes')
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Se debe asignar primero el Establecimiento antes de asignar los departamentos
		</div>		
	@endif
	<!--FIN Mensajes de Guardado o Actualización de Usuarios-->
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Lista de Usuarios</div>
                <div class="panel-body">
                    {{ csrf_field() }} 
					<div class="row">
						<!-- Boton Crear Usuario -->
						<div class="col-md-6">
							<a class="btn btn-sm btn-primary" href="{{ URL::to('users/create') }}">Crear Usuario</a>
						</div>
						<!-- Formulario de Filtro por Correo Electrónico -->
						<div class="col-md-6">
							<form class="form-horizontal" role="form" method="GET" action="{{ URL::to('users') }}">
								<div class="input-group">
									<input id="search" name="search" 	type="text" class="form-control input-sm" placeholder="Buscar por Correo Electrónico">
									<span class="input-group-btn ">
										<button class="btn btn-default btn-sm" type="submit">Ir</button>
									</span>
								</div>
							</form>
						</div>
					</div>
					</br>
					<!-- Lista de Usuarios -->		
					<div class="row">
						<div class="col-md-12">
							<table class="table table-striped">
								<thead>
								  <tr>
									<th>Nombre</th>
									<th>Correo Electrónico</th>
									<th>Estado</th>
									<th>Editar</th>
									<th>Asignar</th>
								  </tr>
								</thead>
								<tbody>
								  @foreach($users as $user)
								  <tr>
									<td>{{ $user->name }}</td>
									<td>{{ $user->email }}</td>
									<td>
										@if( $user->active == 1 )
											Activo
										@else
											Inactivo
										@endif
									</td>
									<td><a href="{{ URL::to('users/' . $user->id . '/edit') }}">Editar</a></td>
									<td><a href="{{ URL::to('users/asignRole/' . $user->id ) }}">Roles</a> | 
									    <a href="{{ URL::to('users/asignEstab/' . $user->id ) }}">Establecimientos</a> | 
										<a href="{{ URL::to('users/asignRef/' . $user->id ) }}">Departamentos</a></td>
								  </tr>
								  @endforeach
								</tbody>
							</table>
							<!--paginacion-->
							{{ $users->links() }}
						</div>
					</div>
					<!-- FIN Lista de Usuarios -->			
                </div>
            </div>
        </div>
    </div>
</div>
@endsection