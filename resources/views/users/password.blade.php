@extends('layouts.app')

@section('content')
<div class="container-fluid">
	<!--Mensajes de Guardado o Actualización de Usuarios-->
	<?php $message=Session::get('message') ?>
	@if($message == 'store')
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Contraseña Se Ha Cambiado Exitosamente
		</div>
	@elseif($message == 'error')
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			Contraseña Actual Incorrecta
		</div>
	@endif	
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
			<!--Panel Formulario Cambiar Password de Usuario-->
			<div class="panel panel-default">
                <div class="panel-heading">Cambiar Contraseña</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ URL::to('users/password/cambiar') }}">
                        {{ csrf_field() }}
						<!--Campo old Password-->
                        <div class="form-group{{ $errors->has('old_password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Contraseña Actual</label>

                            <div class="col-md-6">
                                <input id="old_password" type="password" class="form-control" name="old_password" required>

                                @if ($errors->has('old_password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('old_password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						<!--Campo new Password-->
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Nueva Contraseña</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						<!--Campo Verificar Password-->
                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">Confirmar Nueva Contraseña</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>
						<!--Boton Submit-->
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Cambiar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
			<!--FIN Panel Formulario Crear Usuario-->
        </div>
    </div>
</div>
@endsection

