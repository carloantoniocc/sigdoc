<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/************************************/
/*         MANTENEDORES             */
/************************************/
//CAMBIA VISTA LOGIN COMO INICIO DE SITIO
Route::get('/', 'Auth\LoginController@showLoginForm')->name('login');

//RUTAS DE USUARIO LARAVEL
Auth::routes();

//RUTA DE VISTA, UNA VEZ QUE SE ESTA LOGUEADO
Route::get('/home', 'HomeController@index');

//RUTAS ADMINISTRACION DE USUARIOS
Route::resource('users','UsersController');

//RUTA PARA EL CAMBIO DE PASSWORD
Route::get('users/password/cambiar', 'PasswordUsersController@password');
Route::post('users/password/cambiar', 'PasswordUsersController@save');

//RUTAS ASIGNAR ROLES
Route::get('users/asignRole/{user}', 'UsersController@asignRole');
Route::post('users/saveRole', 'UsersController@saveRole');

//RUTAS ASIGNAR ESTABLECIMIENTOS
Route::get('users/asignEstab/{user}', 'UsersController@asignEstab');
Route::post('users/saveEstab', 'UsersController@saveEstab');

//RUTAS ASIGNAR DEPARTAMENTO (REFERENTE)
Route::get('users/asignRef/{user}', 'UsersController@asignRef');
Route::post('users/saveRef', 'UsersController@saveRef');

//RUTAS TIPO ESTABLECIMIENTO
Route::resource('tipoEstabs','TipoEstabsController');

//RUTAS COMUNAS
Route::resource('comunas','ComunasController');

//RUTA ESTABLECIMIENTOS
Route::resource('establecimientos','EstablecimientosController');

//RUTA PROVEEDORES
Route::resource('proveedors','ProveedorsController');
//alerta de creacion de proveedores si no hay rol asignado
Route::get('/alertaProveedores', function () {
    return view('alertaProveedores');
});

//RUTA REFERENTES
Route::resource('referentes','ReferentesController');

//RUTA TIPO DOCUMENTOS
Route::resource('tipoDocs','TipoDocsController');

//RUTA TIPO COMPRAS
Route::resource('tipoCompras','TipoComprasController');

//RUTA VALIDADORES
Route::resource('validadors','ValidadorsController');

//RUTA TIPO PAGO
Route::resource('tipoPagos','TipoPagosController');

//RUTA BANCO
Route::resource('bancos','BancosController');

//RUTA CLASIFICADORES
Route::resource('clasificadors','ClasificadorsController');

//RUTA CUENTAS
Route::resource('cuentas','CuentasController');

//RUTA OBJETO DE GARANTIA
Route::resource('objetoGarantias','ObjetoGarantiasController');

//RUTA MONEDA
Route::resource('monedas','MonedasController');

//RUTA FIRMANTES
Route::resource('firmantes','FirmantesController');

/************************************/
/*         DOCUMENTOS              */
/************************************/
//RUTAS ADMINISTRADOR
Route::get('administrador/documentos', 'DocumentosController@adminDocumentos')->middleware('admin'); //lista documentos para edicion
Route::get('administrador/{id}/edit/{flujo}', 'DocumentosController@edit')->middleware('admin'); //edicion de documentos
Route::get('administrador/{id}/reversar', 'DocumentosController@reversar')->middleware('admin'); //reversar ultimo movimiento de documentos
Route::post('administrador/reversar','DocumentosController@accionReversar')->middleware('admin'); //accion de reversar ultimo movimiento

//RUTAS OFICINA DE PARTES
Route::get('oficinaPartes', 'DocumentosController@create')->middleware('oficinaPartes'); //crear documento
Route::post('oficinaPartes/store', 'DocumentosController@store')->middleware('oficinaPartes'); //almacenar documento
Route::get('oficinaPartes/nomina', 'DocumentosController@nomina')->middleware('oficinaPartes'); //lista de documentos para nomina
Route::get('oficinaPartes/{id}/edit/{flujo}', 'DocumentosController@edit')->middleware('oficinaPartes'); //edicion de documentos
Route::post('oficinaPartes/generarNomina', 'DocumentosController@generarNomina')->middleware('oficinaPartes'); //genera nomina
Route::get('oficinaPartes/enviaNomina', 'DocumentosController@enviaNomina')->middleware('oficinaPartes'); //envia nomina a secretaria de convenios
Route::get('oficinaPartes/{id}/rechazar/{flujo}', 'DocumentosController@rechazar')->middleware('oficinaPartes'); //llama a formulario de rechazo documentos
Route::get('oficinaPartes/listaNomina','DocumentosController@listaNomina')->middleware('oficinaPartes');//genera lista de nominas generadas
Route::get('oficinaPartes/{nomina}/printNomina','DocumentosController@printNomina')->middleware('oficinaPartes');//genera reporte de nominas
Route::get('oficinaPartes/acepta','DocumentosController@ingresoAcepta')->middleware('oficinaPartes');//Formulario de Ingreso de Archivo Acepta
Route::post('oficinaPartes/uploadAcepta','DocumentosController@uploadAcepta')->middleware('oficinaPartes');//Sube Archivo Acepta


//RUTAS SECRETARIA DE CONVENIOS
Route::get('secretariaConvenios', 'DocumentosController@secretariaConvenios')->middleware('secretariaConvenios'); //lista de documentos para secretaria de convenios
Route::get('secretariaConvenios/{id}/edit/{flujo}', 'DocumentosController@edit')->middleware('secretariaConvenios'); //edicion de documentos
Route::get('secretariaConvenios/{id}/rechazar/{flujo}', 'DocumentosController@rechazar')->middleware('secretariaConvenios'); //llama a formulario de rechazo documentos
Route::post('secretariaConvenios/enviar', 'DocumentosController@enviaSecretariaConvenios')->middleware('secretariaConvenios'); //envia documentos a convenios

//RUTAS CONVENIOS
Route::resource('convenios','ConveniosController');
Route::get('convenios/validadores/{id}', 'ConveniosController@asignValidador'); //asigna validadores de convenios
Route::post('convenios/validadores', 'ConveniosController@saveValidador'); //guarda validadores

//ENVIO A VISTO BUENOS (OFICINA DE CONVENIOS)
Route::get('oficinaConvenios/vistosBuenos','DocumentosController@convenios')->middleware('convenios'); //pantalla envio a vistos buenos por parte de convenios
Route::get('oficinaConvenios/{id}/rechazar/{flujo}', 'DocumentosController@rechazar')->middleware('convenios'); //llama a formulario de rechazo documentos
Route::get('oficinaConvenios/{id}/asignar', 'DocumentosController@envioVB')->middleware('convenios'); //llama a formulario de asignar convenios y enviar a VB
Route::post('oficinaConvenios/asignar', 'DocumentosController@asignaConvenio')->middleware('convenios'); //guarda convenio y envia a VB
Route::get('oficinaConvenios/validados', 'DocumentosController@validados')->middleware('convenios'); //llama a formulario de asignar convenios y enviar a VB
Route::post('oficinaConvenios/validados', 'DocumentosController@envioContabilidad')->middleware('convenios'); //envía documentos a contabilidad
Route::get('oficinaConvenios/{id}/validadores','DocumentosController@validadores')->middleware('convenios'); //accion de visualización de validadores de documentos
Route::get('oficinaConvenios/{id}/devolver', 'DocumentosController@devolverReferente2')->middleware('convenios'); //llama a formulario de devolucion de documentos
Route::post('oficinaConvenios/devolver', 'DocumentosController@movDevolverRt2')->middleware('convenios'); //accion de devolucion documentos
Route::get('oficinaConvenios/documentosAdjuntar','DocumentosController@adjuntarValidadores2')->middleware('convenios'); //lista de documentos para adjuntar validadores
Route::get('oficinaConvenios/{id}/adjuntar/{estado}','DocumentosController@formularioAdjuntar')->middleware('convenios'); //formulario de adjuntar validadores
Route::post('oficinaConvenios/adjuntar','DocumentosController@accionAdjuntar')->middleware('convenios'); //accion de adjuntar validadores

//REFERENTE TECNICO
Route::get('referenteTecnico','DocumentosController@referenteTecnico')->middleware('referenteTecnico'); //pantalla de recepción de convenios
Route::get('referenteTecnico/{id}/rechazar/{flujo}', 'DocumentosController@rechazar')->middleware('referenteTecnico'); //llama a formulario de rechazo documentos
Route::get('referenteTecnico/{id}/devolver', 'DocumentosController@devolverConvenio')->middleware('referenteTecnico'); //llama a formulario de devolucion de documentos
Route::post('referenteTecnico/devolver', 'DocumentosController@movDevolver')->middleware('referenteTecnico'); //accion de devolucion documentos
Route::get('referenteTecnico/{id}/adjuntar', 'DocumentosController@adjuntarValidadores')->middleware('referenteTecnico'); //llama a formulario para adjuntar validadores
Route::post('referenteTecnico/adjuntar', 'DocumentosController@movValidadores')->middleware('referenteTecnico'); //llama a formulario para adjuntar validadores

//Carga Validadores
Route::post('cargaValidadores/uploadcargavalidadores','DocumentosController@uploadcargavalidadores')->middleware('referenteTecnico');
Route::get('cargaValidadores/index','DocumentosController@vistaSelectValidadores')->middleware('referenteTecnico');
Route::get('excelRespuestaCargavalidadores','DocumentosController@excelRespuestaCargavalidadores')->middleware('referenteTecnico');

//JEFE REFERENTE TECNICO
Route::get('jefeReferenteTecnico','DocumentosController@jefeRt')->middleware('jefeRt'); //pantalla de revision de documentos por parte del Jefe de Referente Técnico
Route::get('jefeReferenteTecnico/{id}/validadores','DocumentosController@validadores')->middleware('jefeRt'); //accion de visualización de validadores de documentos
Route::get('jefeReferenteTecnico/{id}/devolver', 'DocumentosController@devolverReferente')->middleware('jefeRt'); //llama a formulario de devolucion de documentos
Route::post('jefeReferenteTecnico/devolver', 'DocumentosController@movDevolverRt')->middleware('jefeRt'); //accion de devolucion documentos
Route::post('jefeReferenteTecnico/enviar', 'DocumentosController@enviarConvenio')->middleware('jefeRt'); //accion de enviar documentos a convenios

//REPORTE REFERENTE TÉCNICO VALIDADOS
Route::get('documentosValidados','DocumentosController@reporteValidados');

//CONTABILIDAD
Route::get('contabilidad','DocumentosController@contabilidad')->middleware('contabilidad'); //pantalla de revision de documentos por parte de Contabilidad
Route::get('contabilidad/{id}/validadores','DocumentosController@validadores')->middleware('contabilidad'); //accion de visualización de validadores de documentos
Route::get('contabilidad/{id}/rechazar/{flujo}', 'DocumentosController@rechazar')->middleware('contabilidad'); //llama a formulario de rechazo documentos
Route::get('contabilidad/{id}/devolver', 'DocumentosController@devolverContabilidad')->middleware('contabilidad'); //llama a formulario de devolucion de documentos
Route::post('contabilidad/devolver', 'DocumentosController@movDevolverCo')->middleware('contabilidad'); //accion de devolucion documentos
Route::get('contabilidad/{id}/devengar', 'DocumentosController@devengar')->middleware('contabilidad'); //llama a formulario para devengo de documentos
Route::post('contabilidad/devengar', 'DocumentosController@movDevengar')->middleware('contabilidad'); //guarda datos de devengo
Route::post('contabilidad/enviar', 'DocumentosController@enviarTesoreria')->middleware('contabilidad'); //accion de enviar documentos a tesoreria
Route::get('contabilidad/reporteContabilidad', 'DocumentosController@reporteContabilidad')->middleware('contabilidad'); //Reporte de contabilidad
Route::get('documentos/excelContabilidad','DocumentosController@excelContabilidad')->middleware('contabilidad');//Exporta a excel el reporte de contabilidad


//TESORERIA
Route::get('tesoreria','DocumentosController@tesoreria')->middleware('tesoreria'); //pantalla de revision de documentos por parte de Tesoreria
Route::get('tesoreria/{id}/validadores','DocumentosController@validadores')->middleware('tesoreria'); //accion de visualización de validadores de documentos
Route::post('tesoreria/pago','DocumentosController@movPago')->middleware('tesoreria'); //guarda datos de pago
Route::get('tesoreria/entrega','DocumentosController@entrega')->middleware('tesoreria'); //pantalla de documentos disponibles para entrega
Route::post('tesoreria/entrega','DocumentosController@movEntrega')->middleware('tesoreria'); //guarda datos de entrega
//TESORERIA-SIGFE
Route::get('tesoreria/sigfe','DocumentosController@ingresoSigfe')->middleware('tesoreria');//Formulario de Ingreso de Archivo SIGFE
Route::post('tesoreria/uploadSigfe','DocumentosController@uploadSigfe')->middleware('tesoreria');//Sube Archivo SIGFE
Route::get('excelRespuestaSIGFE','DocumentosController@excelRespuestaSIGFE')->middleware('tesoreria');//Exporta a excel el resultado Sigfe

//RUTAS APLICACIONES MULTIPLES USUARIOS
Route::put('documentos/{id}', 'DocumentosController@update'); //guarda edicion de documento
Route::post('documentos/rechazar', 'DocumentosController@movRechazar'); //accion de rechazo documentos
Route::get('documentos/reporte','DocumentosController@reporte'); //reporte de documentos
Route::get('documentos/resultado','DocumentosController@resultado'); //resultado consulta de documentos
Route::get('documentos/{id}/validadores','DocumentosController@validadores'); //accion de visualización de validadores de documentos
Route::get('documentos/{id}/bitacora','DocumentosController@bitacora'); //accion de visualización de validadores de documentos
Route::get('documentos/{id}/memoPdf','DocumentosController@memoPdf'); //accion de visualización memo referente tecnico
Route::get('documentos/{id}/memoPdfPre','DocumentosController@memoPdfPre'); //accion de visualización memo referente tecnico preeliminar
Route::get('documentos/{id}/memo2Pdf','DocumentosController@memo2Pdf'); //accion de visualización memo convenios
Route::get('documentos/excel','DocumentosController@excel');//Exporta a excel

/************************************/
/*           GARANTIAS              */
/************************************/
//GARANTIAS - OFICINA DE PARTES
Route::get('garantias/oficinaPartes','GarantiasController@create')->middleware('oficinaPartes');//Ingreso de Garantias
Route::post('garantias/oficinaPartes/store','GarantiasController@store')->middleware('oficinaPartes'); //Almacenar Documento de Garantia
Route::get('garantias/oficinaPartes/nomina','GarantiasController@nomina')->middleware('oficinaPartes'); //lista de garantias para nomina
Route::post('garantias/oficinaPartes/generarNomina', 'GarantiasController@generarNomina')->middleware('oficinaPartes'); //genera nomina
Route::get('garantias/oficinaPartes/{id}/edit/{flujo}', 'GarantiasController@edit')->middleware('oficinaPartes'); //edicion de documentos de garantias
Route::get('garantias/oficinaPartes/enviaNomina', 'GarantiasController@enviaNomina')->middleware('oficinaPartes'); //envia nomina a secretaria de convenios
Route::get('garantias/oficinaPartes/{id}/rechazar/{flujo}', 'GarantiasController@rechazar')->middleware('oficinaPartes'); //llama a formulario de rechazo documentos
Route::get('garantias/oficinaPartes/listaNomina','GarantiasController@listaNomina')->middleware('oficinaPartes');//genera lista de nominas generadas
Route::get('garantias/oficinaPartes/{nomina}/printNomina','GarantiasController@printNomina')->middleware('oficinaPartes');//genera reporte de nominas

//GARANTIAS CONVENIOS
Route::get('garantias/convenios','GarantiasController@convenio')->middleware('convenios');//Bandeja de entrada, Convenios
Route::get('garantias/convenios/{id}/rechazar/{flujo}', 'GarantiasController@rechazar')->middleware('convenios'); //llama a formulario de rechazo documentos
Route::get('garantias/convenios/{id}/edit/{flujo}', 'GarantiasController@edit')->middleware('convenios'); //edicion de documentos de garantias
Route::get('garantias/convenios/{id}/renovar/{flujo}', 'GarantiasController@renovar')->middleware('convenios'); //llama a formulario de renovacion documentos
Route::get('garantias/convenios/{id}/enviar/{flujo}', 'GarantiasController@enviarTesoreria')->middleware('convenios'); //llama a formulario de envio de documento a tesoreria

//GARANTIAS ABASTECIMIENTO
Route::get('garantias/abastecimiento','GarantiasController@abastecimiento')->middleware('abastecimiento');//Bandeja de entrada, Abastecimiento
Route::get('garantias/abastecimiento/{id}/rechazar/{flujo}', 'GarantiasController@rechazar')->middleware('abastecimiento'); //llama a formulario de rechazo documentos
Route::get('garantias/abastecimiento/{id}/edit/{flujo}', 'GarantiasController@edit')->middleware('abastecimiento'); //edicion de documentos de garantias
Route::get('garantias/abastecimiento/{id}/renovar/{flujo}', 'GarantiasController@renovar')->middleware('abastecimiento'); //llama a formulario de renovacion documentos
Route::get('garantias/abastecimiento/{id}/enviar/{flujo}', 'GarantiasController@enviarTesoreria')->middleware('abastecimiento'); //llama a formulario de envio de documento a tesoreria

//GARANTIAS TESORERIA
Route::get('garantias/tesoreria','GarantiasController@tesoreria')->middleware('tesoreria'); //Bandeja de entrada, Tesoreria
Route::get('garantias/tesoreria/{id}/cobrar','GarantiasController@cobrar')->middleware('tesoreria'); //Formulario de Cobro de Boleta de Garantia
Route::get('garantias/tesoreria/{id}/devolver','GarantiasController@devolver')->middleware('tesoreria'); //Formulario de Devolución de Boleta de Garantia
Route::post('garantias/cobrar','GarantiasController@movimientoCobrarDevolver')->middleware('tesoreria'); //Accion de guardar datos de cobro
Route::post('garantias/devolver','GarantiasController@movimientoCobrarDevolver')->middleware('tesoreria'); //Accion de guardar datos de cobro

//RUTAS APLICACIONES GARANTIAS MULTIPLES USUARIOS
Route::put('garantias/{id}', 'GarantiasController@update'); //guarda edicion de documento
Route::post('garantias/rechazar', 'GarantiasController@movRechazar'); //accion de rechazo documentos
Route::post('garantias/renovar', 'GarantiasController@movRenovar'); //accion de renovación de documentos
Route::post('garantias/enviar', 'GarantiasController@movEnviar'); //accion de enviar de documentos a tesoreria
Route::get('garantias/{id}/adjuntos', 'GarantiasController@adjuntos'); //Muestra documentos adjuntos
Route::get('garantias/{id}/memoPdf','GarantiasController@memoPdf'); //accion de visualización memo referente tecnico
Route::get('garantias/reporte','GarantiasController@reporte'); //reporte de documentos
Route::get('garantias/resultado','GarantiasController@resultado'); //resultado consulta de documentos

//RUTA LOGIN AJAX
Route::get('getEstab/{mail}','Auth\LoginController@getEstab');

//VERIFICA SI DOCUMENTO EXISTE EN INGRESO OFICINA DE PARTES
Route::get('getDocumento/{proveedor}/{n_doc}/{tipo}','DocumentosController@docExiste'); 

//RUTA AUTOCOMPLETA PROVEEDOR
Route::get('getProveedor',array('as'=>'getProveedor','uses'=>'DocumentosController@autoComplete'));
Route::get('getProveedorGarantia',array('as'=>'getProveedorGarantia','uses'=>'GarantiasController@autoComplete'));