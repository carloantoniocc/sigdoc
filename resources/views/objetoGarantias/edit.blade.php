@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
			<!--BreadCrumb-->
			<ol class="breadcrumb">
			  <li><a href="{{ URL::to('objetoGarantias') }}">Objetos de Garantía</a></li>
			  <li class="active">Editar</li>
			</ol>
			<!--FIN BreadCrumb-->
			<!--Panel Formulario Editar Objeto de Garantía-->
            <div class="panel panel-default">
                <div class="panel-heading">Editar Objeto de Garantía</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ URL::to('objetoGarantias') }}/{{$objeto->id}}">
                        <input type="hidden" name="_method" value="PUT">
						{{ csrf_field() }}
						<!--Campo Nombre-->
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Nombre</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{$objeto->name}}" readonly>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						
						<!--Flujo-->
						<div class="form-group{{ $errors->has('flujo') ? ' has-error' : '' }}">
                            <label for="flujo" class="col-md-4 control-label">Flujo</label>

                            <div class="col-md-6">
								<select id="flujo" class="form-control" name="flujo" required>
									<option value="">Seleccione</option>
									@if ($objeto->flujo == 1)
										<option value="1" selected>Convenio</option>
										<option value="2">Abastecimiento</option>
									@else
										<option value="1">Convenio</option>
										<option value="2" selected>Abastecimiento</option>
									@endif	
								</select>
                            </div>
                        </div>
						
						<!--Lista Activo-->
						<div class="form-group{{ $errors->has('active') ? ' has-error' : '' }}">
                            <label for="active" class="col-md-4 control-label">Activo</label>

                            <div class="col-md-6">
								<select id="active" class="form-control" name="active" required>
								@if ($objeto->active == 1)
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
			<!--FIN Panel Formulario Objetos de Garantía-->
        </div>
    </div>
</div>
@endsection

