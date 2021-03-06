@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
			<!--BreadCrumb-->
			<ol class="breadcrumb">
			  <li><a href="{{ URL::to('tipoDocs') }}">Tipos de Documento</a></li>
			  <li class="active">Editar</li>
			</ol>
			<!--FIN BreadCrumb-->
			<!--Panel Formulario Editar Tipo Documento-->
            <div class="panel panel-default">
                <div class="panel-heading">Editar Tipo de Documento</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ URL::to('tipoDocs') }}/{{$tipo->id}}">
                        <input type="hidden" name="_method" value="PUT">
						{{ csrf_field() }}
						<!--Campo Nombre-->
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Nombre</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{$tipo->name}}" readonly>
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						<!--Lista Flujo-->
						<div class="form-group{{ $errors->has('flujo') ? ' has-error' : '' }}">
                            <label for="flujo" class="col-md-4 control-label">Flujo</label>

                            <div class="col-md-6">
								<select id="flujo" class="form-control" name="flujo" required>
								@if ($tipo->flujo == 1)
									<option value="1" selected>Factura</option>
									<option value="2">Garantía</option>
								@elseif($tipo->flujo == 2)
									<option value="1">Factura</option>
									<option value="2"  selected>Garantía</option>
								@endif
								</select>
                            </div>
                        </div>
						<!--Lista Orden de Compra-->
						<div class="form-group{{ $errors->has('oc') ? ' has-error' : '' }}">
                            <label for="oc" class="col-md-4 control-label">Orden de Compra</label>

                            <div class="col-md-6">
								<select id="oc" class="form-control" name="oc" required>
								@if ($tipo->oc == 1)
									<option value="1" selected>Si</option>
									<option value="0">No</option>
								@else
									<option value="1">Si</option>
									<option value="0" selected>No</option>
								@endif
								</select>
                            </div>
                        </div>
						<!--Lista Factura Asociada-->
						<div class="form-group{{ $errors->has('asociado') ? ' has-error' : '' }}">
							<label for="asociado" class="col-md-4 control-label">Factura Asociada</label>

							<div class="col-md-6">
								<select id="asociado" class="form-control" name="asociado" required>
								@if ($tipo->asociado == 1)
									<option value="1" selected>Si</option>
									<option value="0">No</option>
								@else
									<option value="1">Si</option>
									<option value="0" selected>No</option>
								@endif
								</select>
							</div>
						</div>
						<!--Lista ID Tipo Doc SII-->
						<div class="form-group{{ $errors->has('id_sii') ? ' has-error' : '' }}">
							<label for="id_sii" class="col-md-4 control-label">ID SII</label>
							
							<div class="col-md-6">
									<input id="id_sii" type="text" class="form-control" name="id_sii" value="{{$tipo->id_sii}}" >
									@if ($errors->has('id_sii'))
											<span class="help-block">
													<strong>{{ $errors->first('id_sii') }}</strong>
											</span>
									@endif
							</div>
						</div>
						<!--Lista ID Resta-->
						<div class="form-group{{ $errors->has('resta') ? ' has-error' : '' }}">
                            <label for="resta" class="col-md-4 control-label">Valor Negativo</label>

                            <div class="col-md-6">
								<select id="resta" class="form-control" name="resta" required>
								@if ($tipo->resta == 1)
									<option value="1" selected>Si</option>
									<option value="0">No</option>
								@else
									<option value="1">Si</option>
									<option value="0" selected>No</option>
								@endif
								</select>
                            </div>
                        </div>
						<!--Lista Activo-->
						<div class="form-group{{ $errors->has('active') ? ' has-error' : '' }}">
                            <label for="active" class="col-md-4 control-label">Activo</label>

                            <div class="col-md-6">
								<select id="active" class="form-control" name="active" required>
								@if ($tipo->active == 1)
									<option value="1" selected>Si</option>
									<option value="0">No</option>
								@else
									<option value="1">Si</option>
									<option value="0" selected>No</option>
								@endif
								</select>
                            </div>
                        </div>
						<!--Boton Submit-->
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Editar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
			<!--FIN Panel Formulario Editar Tipo Establecimiento-->
        </div>
    </div>
</div>
@endsection
