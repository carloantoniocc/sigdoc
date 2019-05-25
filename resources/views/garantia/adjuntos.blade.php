@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
			<!--Panel Formulario Crear Documento-->
			<div class="panel panel-default">
                <div class="panel-heading">Documentos Adjuntos</div>
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
					<br>
					<div class="row">	
						<div class="col-md-10 col-md-offset-1">
							{{ csrf_field() }}
							<table class="table table-striped">
								<thead>
								  <tr>
									<th>Documento</th>
									<th>Archivo</th>
								  </tr>
								</thead>
								@if( $garantia->archivoConformidad != null )
								<tr>
									<td>Certificado de Conformidad</td>
									<td>
										<a href="{{ asset($garantia->archivoConformidad) }}" target="_blank">
											<span class="label label-success"> Adjunto   </span>
										</a>
									</td>
								</tr>
								@endif
								<!--MEMOS AUTOMATICOS-->
								@if( $garantia->memo == 1 )
									@if ( ($mov->estado == 'CG' || $mov->estado == 'DG' || $mov->estado == 'TE') && $objeto->flujo == 1 )
									<tr>
										<td>Memo Convenios / Abastecimiento</td>
										<td>
											<a href="{{ URL::to('garantias/'.$garantia->id.'/memoPdf') }}" target="_blank">
												<span class="label label-success"> Adjunto   </span>
											</a>
										</td>
									</tr>
									@endif
								@endif	
							</table>	
						</div>
					</div>	
				</div>
            </div>
			<!--FIN Panel Formulario Documento-->
        </div>
    </div>
</div>
@endsection

