@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <!--BreadCrumb-->
			<ol class="breadcrumb">
			  <li><a href="{{ URL::to('tipoPagos') }}">Tipos de Pagos</a></li>
			  <li class="active">Crear</li>
			</ol>
			<!--FIN BreadCrumb-->
			<!--Panel Formulario Crear Tipo Pago-->
			<div class="panel panel-default">
                <div class="panel-heading">Crear Tipo de Pago</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ URL::to('tipoPagos') }}">
                        {{ csrf_field() }}
						<!--Campo Nombre-->
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Nombre</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						
						<!--Lista Entrega-->
						<div class="form-group{{ $errors->has('entrega') ? ' has-error' : '' }}">
                            <label for="entrega" class="col-md-4 control-label">Entrega</label>

                            <div class="col-md-6">
								<select id="entrega" class="form-control" name="entrega" required>
								  <option value="1">Si</option>
								  <option value="0">No</option>
								</select>
                            </div>
                        </div>
						
						<!--Lista Tipo de Pago SIGFE-->
						<div class="form-group{{ $errors->has('tipoSigfe') ? ' has-error' : '' }}">
                            <label for="tipoSigfe" class="col-md-4 control-label">Tipo Pago Sigfe</label>

                            <div class="col-md-6">
								<select id="tipoSigfe" class="form-control" name="tipoSigfe">
								  <option value="" selected>Seleccionar</option>
								  <option value="1">Transferencia</option>
								  <option value="3">Cheque</option>
								</select>
                            </div>
                        </div>
						
						<!--Lista Activo-->
						<div class="form-group{{ $errors->has('active') ? ' has-error' : '' }}">
                            <label for="active" class="col-md-4 control-label">Activo</label>

                            <div class="col-md-6">
								<select id="active" class="form-control" name="active" required>
								  <option value="1">Si</option>
								  <option value="0">No</option>
								</select>
                            </div>
                        </div>
						<!--Boton Submit-->
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
			<!--FIN Panel Formulario Crear Tipo Pagos-->
        </div>
    </div>
</div>
@endsection

