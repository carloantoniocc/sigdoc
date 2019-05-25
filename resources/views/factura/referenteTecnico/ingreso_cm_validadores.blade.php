@extends('layouts.app4')

@section('content')
<script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>
<script>
function valorText(){ 
  var valor = document.getElementById('archivo').files.length;  
  if(valor < 2){
    document.getElementById('archi').value = valor + " archivo seleccionado"; 
  }else{
    document.getElementById('archi').value = valor + " archivos seleccionados";
  }
  document.getElementById('div_error_max_size').style.display  = 'none';
}
function validaFiles() {
  var control = document.getElementById("archivo");
  var filelength = control.files.length;
  if(filelength > 100){
    event.preventDefault();
    document.getElementById('div_maximo').style.display  = 'block';
  } 
  var combinedSize = 0;
  for (var i = 0; i < control.files.length; i++) {
      combinedSize += (control.files[i].size||control.files[i].fileSize)/1024/1024; // De byte a kB , de kB a MB
  }
  if(combinedSize>1024){ // consulta si es mayor a un GB
          event.preventDefault();
          document.getElementById('div_error_max_size').style.display  = 'block';
          //alert(Math.round(combinedSize*1024));          
  }  
}
</script>
<div class="container-fluid">
  <!--Mensajes de Guardado o Actualización de Documentos-->
  <?php $message=Session::get('message') ?>
  
  <!--FIN Mensajes de Guardado o Actualización de Documentos-->
  <div class="row">
    <div class="col-lg-10 col-lg-offset-1 col-md-12">
      <!--Panel Formulario Subir Documentos Acepta-->
      <div class="panel panel-default">
        <div class="panel-heading">Adjuntar archivos</div>
        <div class="panel-body">        
          <!--ALERTA NUEVA APLICACION-->
          <div class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>Nueva Funcionalidad</strong> 
            <br>
            Para que "Validador - Carga Masiva" opere correctamente, se debe tener en cosideración los siguientes puntos:
            <br>
            - La funcionalidad operará sólo para los documentos que se encuentran en estado "Por Validar" en la bandeja de documentos del referente técnico.
            <br>
            - El tipo de validador seleccionado, debe coincidir con el validador requerido por el convenio asignado al documento.
            <br>
            - Los nombres del archivos a adjuntar deben coincidir con el número del documento tributario.
            <br>
            En caso de dudas consultar manual del referente técnico.
          </div>         
          <div class="row"> 
            <div class="col-md-10 col-md-offset-1">              
              <br>
              <form id="form1" class="form-horizontal" role="form" method="POST" action="{{ URL::to('cargaValidadores/uploadcargavalidadores') }}"
              enctype="multipart/form-data" accept-charset="utf-8" onsubmit="validaFiles()" enctype="multipart/form-data">
              {{ csrf_field() }}
                <div class="row">
                  <div class="col-md-11 form-group">
                    <div class="col-md-3">
                      <select id="tipo" class="form-control" name="tipo">
                        <option value="">Seleccione tipo de validador</option>
                        @foreach($consulta as $tipo)
                        <option value="{{ $tipo->id }}">{{ $tipo->name }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="input-group col-md-9">
                      <label class="input-group-btn">
                        <span class="btn btn-default">
                          <img alt="" src="{{ asset('image/upload-box-solid.png') }}" style="heigth:16px; width:16px;">
                          <input type="file" class="form-control" id="archivo" name="archivo[]" style="display: none;" multiple  onChange="valorText()" accept="application/pdf" >                          
                        </span>
                      </label>
                      <input type="text" class="form-control" name="archi" id="archi" value="" placeholder="" readonly>
                    </div>
                    
                    @if ($errors->has('archivo') and $errors->has('tipo'))
                    <div class="input-group col-md-9 col-md-offset-3 has-error">
                      <span class="help-block">
                        <strong>Campos Vacios</strong>
                      </span>
                    </div>
                     @elseif ($errors->has('tipo'))
                    <div class="input-group col-md-9 col-md-offset-3 has-error">
                      <span class="help-block">
                        <strong>Debe seleccionar un tipo de documento</strong>
                      </span>
                    </div>
                     @elseif ($errors->has('archivo'))
                    <div class="input-group col-md-9 col-md-offset-3 has-error">
                      <span class="help-block">
                        <strong>Debe seleccionar archivos para adjuntar</strong>
                      </span>
                    </div>
                    @elseif ($errors->has('maximo'))
                    <div class="input-group col-md-9 col-md-offset-3 has-error">
                      <span class="help-block">
                        <strong>La cantidad de datos adjuntos no deben ser mayor a 100</strong>
                      </span>
                    </div>
                    @endif
                    <div id="div_error_max_size" class="input-group col-md-9 col-md-offset-3 has-error" style="display : none;">
                      <span class="help-block">
                        <strong>Los archivos adjuntos no deben tener en total un tamaño mayor a 1 GB</strong>
                      </span>
                    </div>
                    <div id = "div_maximo" class="input-group col-md-9 col-md-offset-3 has-error" style="display : none;">
                      <span class="help-block">
                        <strong>La cantidad de datos adjuntos no deben ser mayor a 100</strong>
                      </span>
                    </div>    
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-md-11 form-group">
                    <div class="col-md-3">
                      <button type="submit" class="btn btn-primary">
                        Guardar
                      </button>
                    </div>
                    <div class="input-group col-md-9">
                      <span class="help-block">
                        Nota: Documentos en formato PDF (hasta 10 MB c/u) y el total de los documentos no debe exceder a 1GB
                      </span>
                    </div>
                  </div>  
                </div>
          <!--fin from -->              
              </form>
            </div>
          </div>  
        </div>
      </div>
      <!--FIN Panel Formulario Documento-->
    </div>
  </div>
</div>

@endsection
