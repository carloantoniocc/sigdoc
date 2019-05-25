@extends('layouts.app')

@section('content')
<div class="container-fluid">
	<!--Alerta Explorador IExplorer-->
	@if ((isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)) || strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== false)
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<strong>Aviso:</strong> Este Navegador de Internet no está soportado para esta aplicación. Se recomienda el uso de <strong>Google Chrome</strong> o <strong>Mozilla Firefox</strong>
		</div>
	@endif
	<div class="row">
        <div class="col-lg-10 col-lg-offset-1 col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Inicio</div>

                <div class="panel-body">
                    <div class="col-md-4">
						<div class="logodti">	
							<img alt="" src="{{ asset('image/logoDTI.png') }}">
						</div>
						<br><br>
					</div>
					<div class="col-md-8">
						<br><br>
						<p>Bienvenido al Sistema de Gestión Documentas SIGDOC.</b></p>
						
						<p>Este sistema a sido desarrollado por el Departamento de Tecnologías de la Información del Servicio de Salud Metropolitano Occidente.</p>
						
						<p>Ante alguna duda, consulta o sugerencia, contactenos al correo <a href="mailto:soporte.ssmoc@redsalud.gov.cl">soporte.ssmoc@redsalud.gov.cl</a></p>
					</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
