@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
			<!--BreadCrumb-->
			<ol class="breadcrumb">
			  <li><a href="{{ URL::to('referentes') }}">Referentes</a></li>
			  <li class="active">Editar</li>
			</ol>
			<!--FIN BreadCrumb-->
			<!--Panel Formulario Editar Referente-->
            <div class="panel panel-default">
                <div class="panel-heading">Editar Referente</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ URL::to('referentes') }}/{{$referente->id}}">
                        <input type="hidden" name="_method" value="PUT">
						{{ csrf_field() }}
						<!--Campo Nombre-->
						<div class="form-group">
							<label for="name" class="col-md-4 control-label">Nombre</label>
							
							<div class="col-md-6">
								<input id="name" type="text" class="form-control" name="name" value="{{$referente->name}}" readonly>
							</div>
						</div>	
						
						<!--Campo Establecimiento-->
						<div class="form-group">
							<label for="establecimiento" class="col-md-4 control-label">Establecimiento</label>

							<div class="col-md-6">
								<input id="establecimiento" type="text" class="form-control" name="establecimiento" value="{{$establecimiento->name}}" readonly>
							</div>
						</div>
						
						<!--Lista Activo-->
						<div class="form-group{{ $errors->has('active') ? ' has-error' : '' }}">
                            <label for="active" class="col-md-4 control-label">Activo</label>

                            <div class="col-md-6">
								<select id="active" class="form-control" name="active" required>
								@if ($referente->active == 1)
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
			<!--FIN Panel Formulario Editar Referente-->
        </div>
    </div>
</div>
@endsection

