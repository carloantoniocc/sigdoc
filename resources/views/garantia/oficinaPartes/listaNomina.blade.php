@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Lista de Nóminas - Garantías</div>
                <div class="panel-body">
				{{ csrf_field() }} 
					<!-- Lista de Documentos -->		
					<div class="row">
						<div class="col-md-12">
							<table class="table table-striped">
								<thead>
								  <tr>
									<th>Fecha</th>
									<th>Nominas</th>
									<th>Acciones</th>
								  </tr>
								</thead>
								<tbody>
								  @foreach($garantias as $garantia)
								  <tr>
									<td>{{ date('d/m/Y',$garantia->nomina) }}</td>
									<td>{{ $garantia->nomina }}</td>
									<td>
										<a href="{{ URL::to('garantias/oficinaPartes/' . $garantia->nomina . '/printNomina') }}" target="_blank" title="Imprimir Nomina">Imprimir</a> 
									</td>
								  </tr>
								  @endforeach
								</tbody>
							</table>
							<!--paginacion-->
							{{ $garantias->links() }}
						</div>
					</div>
					<!-- FIN Lista de Documentos -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection