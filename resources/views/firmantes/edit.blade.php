@extends('layouts.app4')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <!--BreadCrumb-->
			<ol class="breadcrumb">
			  <li><a href="{{ URL::to('firmantes') }}">Firmantes</a></li>
			  <li class="active">Editar</li>
			</ol>
			<!--FIN BreadCrumb-->
			<!--Panel Formulario Editar Firmante-->
			<div class="panel panel-default">
                <div class="panel-heading">Editar Firmante Memos</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ URL::to('firmantes') }}/{{$firmante->id}}">
						<input type="hidden" name="_method" value="PUT">
                        {{ csrf_field() }}
						<!--Campo Usuario-->
                        <div class="form-group">
                            <label for="user_id" class="col-md-4 control-label">Usuario</label>
                            <div class="col-md-6">
								<input id="user_id" type="text" class="form-control" name="user_id" value="{{$usuario->name}}" readonly>
                            </div>
                        </div>
						<!--Campo Establecimiento-->
                        <div class="form-group">
                            <label for="establecimiento_id" class="col-md-4 control-label">Establecimiento</label>
                            <div class="col-md-6">
								<input id="establecimiento_id" type="text" class="form-control" name="establecimiento_id" value="{{$establecimiento->name}}" readonly>
                            </div>
                        </div>
						<!--Lista Cargo-->
						<div class="form-group">
                            <label for="memo_id" class="col-md-4 control-label">Cargo</label>
                            <div class="col-md-6">
								@if ($firmante->memo_id == 1)
									<input id="memo_id" type="text" class="form-control" name="memo_id" value="Jefe de Convenios" readonly>
								@elseif ($firmante->memo_id == 2)
									<input id="memo_id" type="text" class="form-control" name="memo_id" value="Jefe de Gestión Financiera" readonly>
                                @endif
                            </div>
                        </div>
						<!--Fecha Desde-->
						<div class="form-group{{ $errors->has('fechaDesde') ? ' has-error' : '' }}">
							<label for="fechaDesde" class="col-md-4 control-label">Fecha Desde</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="fechaDesde" id="fechaDesde" value="{{ $firmante->fechaDesde }}" maxlength="10" placeholder="dd-mm-yyyy" required autofocus>
								@if ($errors->has('fechaDesde'))
								<span class="help-block">
									<strong>{{ $errors->first('fechaDesde') }}</strong>
								</span>
								@endif
							</div>
						</div>
						<!--Fecha Hasta-->
						<div class="form-group{{ $errors->has('fechaHasta') ? ' has-error' : '' }}">
							<label for="fechaHasta" class="col-md-4 control-label">Fecha Hasta</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="fechaHasta" id="fechaHasta" value="{{ $firmante->fechaHasta }}" maxlength="10" placeholder="dd-mm-yyyy" required autofocus>
								@if ($errors->has('fechaHasta'))
								<span class="help-block">
									<strong>{{ $errors->first('fechaHasta') }}</strong>
								</span>
								@endif
							</div>
						</div>
						<!--Lista Activo-->
						<div class="form-group{{ $errors->has('active') ? ' has-error' : '' }}">
                            <label for="active" class="col-md-4 control-label">Activo</label>

                            <div class="col-md-6">
								<select id="active" class="form-control" name="active" required autofocus>
									@if ($firmante->active == 1)
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
			<!--FIN Panel Formulario Crear Firmantes-->
        </div>
    </div>
</div>
<!--Script Calendario-->
<script>
$('#fechaDesde').datepicker({
        dateFormat: "dd-mm-yy",
        firstDay: 1,
		dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
        dayNamesShort: ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"],
        monthNames: 
            ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio",
            "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
        monthNamesShort: 
            ["Ene", "Feb", "Mar", "Abr", "May", "Jun",
            "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
		//focus
		onSelect: function ()
		{
			this.focus();
		}
});

//funcion que pone mascara de fecha
document.getElementById('fechaDesde').addEventListener('keyup', function() {
	var v = this.value;
	if (v.match(/^\d{2}$/) !== null) {
		this.value = v + '-';
	} else if (v.match(/^\d{2}\-\d{2}$/) !== null) {
		this.value = v + '-';
	}
});		

$('#fechaHasta').datepicker({
        dateFormat: "dd-mm-yy",
        firstDay: 1,
        dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
        dayNamesShort: ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"],
        monthNames: 
            ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio",
            "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
        monthNamesShort: 
            ["Ene", "Feb", "Mar", "Abr", "May", "Jun",
            "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
		//focus
		onSelect: function ()
		{
			this.focus();
		}
});

//funcion que pone mascara de fecha
document.getElementById('fechaHasta').addEventListener('keyup', function() {
	var v = this.value;
	if (v.match(/^\d{2}$/) !== null) {
		this.value = v + '-';
	} else if (v.match(/^\d{2}\-\d{2}$/) !== null) {
		this.value = v + '-';
	}
});	
</script>
@endsection

