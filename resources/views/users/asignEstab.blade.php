@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
			<!--BreadCrumb-->
			<ol class="breadcrumb">
			  <li><a href="{{ URL::to('users') }}">Usuarios</a></li>
			  <li class="active">Asignar Establecimiento</li>
			</ol>
			<!--FIN BreadCrumb-->
            <div class="panel panel-default">
				<div class="panel-heading">Asignar Establecimientos de Usuario</div>
				<div class="panel-body">
					<form class="form-horizontal" role="form" method="POST" action="{{ URL::to('users/saveEstab') }}">
						{{ csrf_field() }} 
						<!--Lista de Selección Multiple-->
						<div class="form-group">
                            <label for="estabUsuarios" class="col-md-4 control-label">Establecimientos</label>

                            <div class="col-md-6">
								<select id="estabUsuarios" name="estabUsuarios[]" class="form-control" multiple size="10" required>
									@foreach($establecimientos as $establecimiento)
										@if($user->isEstab($establecimiento->name))
											<option value="{{ $establecimiento->id }}" selected>{{ $establecimiento->name }}</option>
										@else
											<option value="{{ $establecimiento->id }}">{{ $establecimiento->name }}</option>
										@endif
									@endforeach
								</select>    
                            </div>
                        </div>
						<!--ID Usuario-->
						<input type="hidden" name="userID" id="userID" value="{{$id}}">
						
						<!--Boton Submit-->
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Asignar
                                </button>
                            </div>
                        </div>
					</form>
				</div>
			</div>
        </div>
    </div>
</div>
@endsection