@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
		<div class="col-lg-10 col-lg-offset-1 col-md-12">
			<!--BreadCrumb-->
			<ol class="breadcrumb">
			  <li><a href="{{ URL::to('convenios') }}">Convenios</a></li>
			  <li class="active">Asignar Validadores</li>
			</ol>
			<!--FIN BreadCrumb-->
            <div class="panel panel-default">
				<div class="panel-heading">Asignar Validadores de Convenios</div>
				<div class="panel-body">
					<form class="form-horizontal" role="form" method="POST" action="{{ URL::to('convenios/validadores') }}">
						{{ csrf_field() }} 
						<!--Lista de Selección Multiple-->
						<div class="form-group">
                            <label for="validadores" class="col-md-4 control-label">Validadores</label>

                            <div class="col-md-6">
								<select id="validadores" name="validadores[]" class="form-control" multiple size="10">
									@foreach($validadors as $validador)
										@if($convenio->isValidador($validador->name))
											<option value="{{ $validador->id }}" selected>{{ $validador->name }}</option>
										@else
											<option value="{{ $validador->id }}">{{ $validador->name }}</option>
										@endif
									@endforeach
								</select>    
                            </div>
                        </div>
						<!--ID Convenio-->
						<input type="hidden" name="id" id="id" value="{{$id}}">
						
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