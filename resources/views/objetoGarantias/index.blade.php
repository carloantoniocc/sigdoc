@extends('layouts.app')

@section('content')
<div class="container-fluid">
	<!--Mensajes de Guardado o Actualización de Objetos de Garantía-->
	<?php $message=Session::get('message') ?>
	@if($message == 'store')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Objeto de Garantía Creado Exitosamente
		</div>
	@elseif($message == 'update')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Objeto de Garantía Modificado Exitosamente
		</div>
	@endif
	<!--FIN Mensajes de Guardado o Actualización de Objetos de Garantía-->
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Lista de Objetos de Garantía</div>
                <div class="panel-body">
                    {{ csrf_field() }} 
					<div class="row">
						<!-- Boton Crear Nuevo Objeto de Garantía -->
						<div class="col-md-6">
							<a class="btn btn-sm btn-primary" href="{{ URL::to('objetoGarantias/create') }}">Crear Objeto de Garantía</a>
						</div>
						
					</div>
					</br>
					<!-- Lista de Objetos de Garantía -->		
					<div class="row">
						<div class="col-md-12">
							<table class="table table-striped">
								<thead>
								  <tr>
									<th>Nombre</th>
									<th>Flujo</th>
									<th>Estado</th>
									<th>Editar</th>
								  </tr>
								</thead>
								<tbody>
								  @foreach($objetos as $objeto)
								  <tr>
									<td>{{ $objeto->name }}</td>
									<td>
										@if( $objeto->flujo == 1 )
											Convenio
										@else
											Abastecimiento
										@endif
									</td>
									<td>
										@if( $objeto->active == 1 )
											Activo
										@else
											Inactivo
										@endif
									</td>
									<td><a href="{{ URL::to('objetoGarantias/' . $objeto->id . '/edit') }}">Editar</a></td>
								  </tr>
								  @endforeach
								</tbody>
							</table>
							<!--paginacion-->
							{{ $objetos->links() }}
						</div>
					</div>
					<!-- FIN Lista de Objetos de Garantia -->			
                </div>
            </div>
        </div>
    </div>
</div>
@endsection