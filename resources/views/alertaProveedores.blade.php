@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Alerta Creación de Proveedores</div>

                <div class="panel-body">
                    <div class="alert alert-warning">
						<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
						No posee permisos para la creación de proveedores. Por favor comuniquese con su administrador.
					</div>	
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
