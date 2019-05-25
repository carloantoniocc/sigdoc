@extends('layouts.app')

@section('content')
<div class="container-fluid">
	<!--Mensajes de Guardado o Actualización de Firmantes-->
	<?php $message=Session::get('message') ?>
	@if($message == 'store')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Firmante Creado Exitosamente
		</div>
	@elseif($message == 'update')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Firmante Modificado Exitosamente
		</div>
	@endif
	<!--FIN Mensajes de Guardado o Actualización de Firmantes-->
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Lista de Firmantes</div>
                <div class="panel-body">
                    {{ csrf_field() }} 
					<div class="row">
						<!-- Boton Crear Nuevo Firmante -->
						<div class="col-md-6">
							<a class="btn btn-sm btn-primary" href="{{ URL::to('firmantes/create') }}">Crear Firmante</a>
						</div>
						
					</div>
					</br>
					<!-- Lista de Firmantes -->		
					<div class="row">
						<div class="col-md-12">
							<table class="table table-striped">
								<thead>
								  <tr>
									<th>Nombre</th>
									<th>Establecimiento</th>
									<th>Cargo</th>
									<th>Desde</th>
									<th>Hasta</th>
									<th>Estado</th>
									<th>Editar</th>
								  </tr>
								</thead>
								<tbody>
								  @foreach($firmantes as $firmante)
								  <tr>
									<td>{{ $firmante->usuario }}</td>
									<td>{{ $firmante->establecimiento }}</td>
									<td>
										@if( $firmante->memo_id == 1 )
											Jefe de Convenios
										@else
											Jefe de Gestión Financiera
										@endif
									</td>
									<td>{{ $firmante->fechaDesde }}</td>
									<td>{{ $firmante->fechaHasta }}</td>
									<td>
										@if( $firmante->active == 1 )
											Activo
										@else
											Inactivo
										@endif
									</td>
									<td><a href="{{ URL::to('firmantes/' . $firmante->id . '/edit') }}">Editar</a></td>
								  </tr>
								  @endforeach
								</tbody>
							</table>
							<!--paginacion-->
							{{ $firmantes->links() }}
						</div>
					</div>
					<!-- FIN Lista de Firmantes -->			
                </div>
            </div>
        </div>
    </div>
</div>
@endsection