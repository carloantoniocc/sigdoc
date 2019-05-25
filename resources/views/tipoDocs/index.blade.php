@extends('layouts.app')

@section('content')
<div class="container-fluid">
	<!--Mensajes de Guardado o Actualización de Tipos-->
	<?php $message=Session::get('message') ?>
	@if($message == 'store')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Tipo de Documento Creado Exitosamente
		</div>
	@elseif($message == 'update')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Tipo de Documento Modificado Exitosamente
		</div>
	@endif
	<!--FIN Mensajes de Guardado o Actualización de Tipos-->
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Lista de Tipos de Docuento</div>
                <div class="panel-body">
                    {{ csrf_field() }} 
					<div class="row">
						<!-- Boton Crear Nuevo Tipo -->
						<div class="col-md-6">
							<a class="btn btn-sm btn-primary" href="{{ URL::to('tipoDocs/create') }}">Crear Tipo Documento</a>
						</div>
						
					</div>
					</br>
					<!-- Lista de Tipos de Documento -->		
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
								  @foreach($tipos as $tipo)
								  <tr>
									<td>{{ $tipo->name }}</td>
									<td>
										@if( $tipo->flujo == 1 ) <!--Factura-->
											Factura
										@elseif( $tipo->flujo == 2 ) <!--Garantía-->
											Garantía
										@endif
									</td>
									<td>
										@if( $tipo->active == 1 )
											Activo
										@else
											Inactivo
										@endif
									</td>
									<td><a href="{{ URL::to('tipoDocs/' . $tipo->id . '/edit') }}">Editar</a></td>
								  </tr>
								  @endforeach
								</tbody>
							</table>
							<!--paginacion-->
							{{ $tipos->links() }}
						</div>
					</div>
					<!-- FIN Lista de tipos de Documentos -->			
                </div>
            </div>
        </div>
    </div>
</div>
@endsection