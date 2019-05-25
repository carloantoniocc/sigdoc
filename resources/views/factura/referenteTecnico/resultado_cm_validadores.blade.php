@extends('layouts.app4')

@section('content')
<div class="container-fluid">
  <!--Mensajes de Guardado o Actualización de Documentos-->
  <?php $message=Session::get('message') ?>
  
  <!--FIN Mensajes de Guardado o Actualización de Documentos-->
  <div class="row">
    <div class="col-lg-10 col-lg-offset-1 col-md-12">
      <!--Panel Formulario Subir Documentos Acepta-->
      <div class="panel panel-default">
        <div class="panel-heading">Archivos adjuntos</div>
        <div class="panel-body">
          {{ csrf_field() }} 
          <br><br>
          <div class="row"> 
            <div class="col-md-10 col-md-offset-1">
         <!--Avisa cuantos documentos fueron ingresados-->
         <div class="row row-padding row-border">
            <div class="col-md-12">
              <div class="alert alert-success" role="alert">
                Se ingresaron correctamente <strong>{{ $cont }} documentos</strong>
              </div>
            </div>
          </div> 
          <br>
            <!-- Botón para exportar a excel-->           
          <div class="row">
            {{ csrf_field() }} 
            <div class="col-md-9">              
              <form class="form-horizontal" role="form" method="GET" action="{{ URL::to('excelRespuestaCargavalidadores') }}">  
                <input type="submit" name="excel" id="excel" value="Exportar a Excel" class="btn btn-sm btn-primary"></input>
              </form>
            </div>
          </div>  
          <br>
          @for($i=1;$i<=count($error_count);$i++)
          <!--Error -->
            @if($error_count[$i] > 0)
              <div class="row row-border">
                <div class="col-xs-10 col-sm-11 col-md-11">
                  <h5>{{ $mensaje[$i] }} <strong>({{$error_count[$i]}} encontrados)</strong>.</h5>
                </div>
                <div class="col-xs-1 col-sm-1 col-md-1">
                  <h5>
                    <a href="#" class="pull-right"  data-toggle="collapse" data-target="#error{{$i}}">
                      <span class="glyphicon glyphicon-plus"></span>
                    </a>
                  </h5>
                </div>
              </div>
              </br>
              <div id="error{{$i}}" class="collapse">
                <div class="row row-padding">
                  <div class="col-md-12">
                    <table class="table table-striped">
                      <tbody>
                        @foreach($errores as $respuesta)
                          @php
                            $respuesta = explode("::", $respuesta);
                          @endphp
                          @if($respuesta[2]==$i)
                            <tr>
                              <td><span class="label label-danger">No Cargado</span></td>
                              <td> {{ $respuesta[0] }}</td>
                            </tr>
                          @endif
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            @endif
            <br>
          @endfor  
         </div>
          </div>
        </div>
      </div>
      <!--FIN Panel Formulario Documento-->
    </div>
  </div>
</div>
@endsection
