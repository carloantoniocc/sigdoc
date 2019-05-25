@extends('layouts.app')

@section('content')
<div class="container-fluid">
	<!--Mensajes de Guardado o Actualización de Clasificadores-->
	<?php $message=Session::get('message') ?>
	@if($message == 'store')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Clasificador Creado Exitosamente
		</div>
	@elseif($message == 'update')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Clasificador Modificado Exitosamente
		</div>
	@endif
	<!--FIN Mensajes de Guardado o Actualización de Clasificadores-->
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Lista de Clasificadores</div>
                <div class="panel-body">
                    {{ csrf_field() }} 
					<div class="row">
						<!-- Boton Crear Nueva Clasificador -->
						<div class="col-md-6">
							<a class="btn btn-sm btn-primary" href="{{ URL::to('clasificadors/create') }}">Crear Clasificador</a>
						</div>
						
					</div>
					</br>
					<!-- Lista de Clasificadores -->		
					<div class="row">
						<div class="col-md-12">
							<table class="table table-striped">
								<thead>
								  <tr>
									<th>Código</th>
									<th>Nombre</th>
									<th>Estado</th>
									<th>Editar</th>
								  </tr>
								</thead>
								<tbody>
								  @foreach($clasificadors as $clasificador)
								  <tr>
									<td>{{ $clasificador->codigo }}</td>
									<td>{{ $clasificador->name }}</td>
									<td>
										@if( $clasificador->active == 1 )
											Activo
										@else
											Inactivo
										@endif
									</td>
									<td><a href="{{ URL::to('clasificadors/' . $clasificador->id . '/edit') }}">Editar</a></td>
								  </tr>
								  @endforeach
								</tbody>
							</table>
							<!--paginacion-->
							{{ $clasificadors->links() }}
						</div>
					</div>
					<!-- FIN Lista de Clasificadores -->			
                </div>
            </div>
        </div>
    </div>
</div>
@endsection