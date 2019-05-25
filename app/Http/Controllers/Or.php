<?php

namespace sigdoc\Http\Controllers; 

use Illuminate\Http\Request;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use sigdoc\Documento;
use sigdoc\Proveedor;
use sigdoc\TipoDoc;
use sigdoc\Movimiento;
use sigdoc\Convenio;
use sigdoc\DocumentoValidador;
use sigdoc\Cuenta;
use sigdoc\TipoPago;
use sigdoc\User;
use sigdoc\Referente;
use sigdoc\Establecimiento;
use sigdoc\Clasificador;
use sigdoc\Validador;
use sigdoc\Firmante;
use sigdoc\OracleDocumento;

use DateTime;
use PDF;
use Excel;
use Response;

use DB;
use Illuminate\Support\Facades\Auth;

use sigdoc\Notifications\ReferenteMail;
use sigdoc\Notifications\RechazoMail;

use Exception;

use Session;

/**
 * Clase Controlador Documentos Tributarios
 * Rol: Por Funcion
 */
class DocumentosController extends Controller
{
    /*******************************************************************************************/

	/*                             OFICINA DE PARTES                                           */
	/*******************************************************************************************/
	/**
     * Funcion que Crea Documento Tributario en Oficina de Partes
	 * Vista: factura.oficinaPartes.create
	 * Rol: oficinaPartes
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
		if (Auth::check()) {
			$tipos = TipoDoc::where([['active',1],['flujo',1]])->orderBy('name')->get();

			return view('factura.oficinaPartes.create',compact('tipos'));		
		}
		else {
			return view('auth/login');
		}
    }
	
	/**
     * Funcion que Almacena Documento Tributario Creado por Oficina de Partes.
	 * Rol: oficinaPartes
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		if (Auth::check()) {
			
			// validate requeridos
			$validator = validator::make($request->all(), [
				'proveedor' => 'required',
				'nDoc' => 'required|integer|min:0',
				'tipo' => 'required',
				'facAsociada' => 'nullable|integer|min:0',
				'fechaRecepcion' => 'required',
				'fechaEmision' => 'required',
				'fechaVencimiento' => 'required',
				'monto' => 'required|integer|min:0',
				'ordenCompra' => 'nullable|string|max:15',
				'observacion' => 'nullable|string|max:150',
				"archivo" => "nullable|mimes:pdf|max:10024",
			]);
			
			if ($validator->fails()) {
				return redirect('oficinaPartes')
							->withErrors($validator)
							->withInput();
			}
			else {
				//determina el ID del proveedor
				$explode = explode("-",$request->proveedor);
				$rut = $explode[0];
				$proveedor = Proveedor::where('rut',$rut)->first();
				
				//si proveedor no existe
				if ($proveedor == null) {
					return redirect('/oficinaPartes')->with('message','proveedor')->withInput();
				}
								
				//tipo de documento
				$tipoExplode = explode("-",$request->tipo);
				$tipoValue   = $tipoExplode[0];
				
				//verifica que documento no exista
				$valor = Documento::where([['nDoc',$request->nDoc],['proveedor_id',$proveedor->id],['tipoDoc_id',$tipoValue]])->count();
				if ($valor > 0) {
					return redirect('/oficinaPartes')->with('message','documento')->withInput();
				}
								
				//adjunta archivo
				$archivoName = null;
				
				if ($request->hasFile('archivo')) {
					$archivo = $request->file('archivo');
					$archivoName = 'd'.time().$archivo->getClientOriginalName();
					
					//guarda archivo
					$request->file('archivo')->storeAs('public',$archivoName);
				}

				//formatea fechas
				$fechaEmision = DateTime::createFromFormat('d-m-Y', $request->fechaEmision);
				$fechaRecepcion = DateTime::createFromFormat('d-m-Y', $request->fechaRecepcion);
				$fechaVencimiento = DateTime::createFromFormat('d-m-Y', $request->fechaVencimiento);
				
				
				//establecimiento usuario
				//$estab_user = $request->session()->get('establecimiento');
				$user       = User::find(Auth::user()->id);
				$estab_user = $user->establecimientos()->first()->id;
				
				//guarda datos de documento
				$documento = new Documento;
				
				$documento->proveedor_id = $proveedor->id;
				$documento->tipoDoc_id = $tipoValue;
				$documento->establecimiento_id = $estab_user;
				$documento->nDoc = $request->nDoc;
				$documento->facAsociada = $request->facAsociada;
				$documento->fechaEmision = $fechaEmision;
				$documento->fechaRecepcion = $fechaRecepcion;
				$documento->fechaVencimiento = $fechaVencimiento;
				$documento->monto = $request->monto;
				$documento->ordenCompra = $request->ordenCompra;
				if($archivoName != null) {
					$documento->archivo = Storage::url($archivoName);
				}
				$documento->user_id = Auth::user()->id;
				
				$documento->save();
				
				//guarda movimiento
				if ( $documento->id != null ) {
					$movimiento = new Movimiento;
					
					$movimiento->documento_id = $documento->id;
					$movimiento->estado = 'OP';
					$movimiento->observacion = str_replace(PHP_EOL,"<br>",$request->observacion);
					$movimiento->user_id = Auth::user()->id;
					
					$movimiento->save();
				}
				
				return redirect('/oficinaPartes')->with('message','store');
			}	
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Funcion que Lista los Documentos Tributarios que fueron ingresados en Oficina de Partes en estado OP.
	 * Vista: factura.oficinaPartes.nomina
	 * Rol: oficinaPartes
     *
     * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
     */
    public function nomina(Request $request)
	{	
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;
			
			$documentos =  DB::table('documentos')
							->join('movimientos', 'documentos.id','=','movimientos.documento_id')
							->join('proveedors','documentos.proveedor_id','=','proveedors.id')
							->join('tipo_docs','documentos.tipoDoc_id','=','tipo_docs.id')
							->select('proveedors.rut as rut',
									 'proveedors.name as nameProveedor',
									 'tipo_docs.name as tipoDoc',
									 'documentos.id as id',
									 'documentos.nDoc as nDoc',
									 'documentos.archivo as archivo',
									 'documentos.nomina as nomina',
									 'documentos.ordenCompra as ordenCompra')
							->selectRaw('DATE_FORMAT(documentos.fechaRecepcion, "%d-%m-%Y") as fechaRecepcion')
							->where([
									['documentos.establecimiento_id','=',$estab_user],
									['movimientos.active',1],
									['movimientos.estado','OP'],
									['rut', 'LIKE', '%'.$request->get('searchRut').'%'],
									['tipo_docs.id', 'LIKE', '%'.$request->get('searchTipo').'%'],
									['nDoc', 'LIKE', '%'.$request->get('searchNdoc').'%'],
									])
							->orderBy('documentos.id')->paginate(500)
							->appends('searchRut',$request->get('searchRut'))
							->appends('searchTipo',$request->get('searchTipo'))
							->appends('searchNdoc',$request->get('searchNdoc'));
			
			$tipos = TipoDoc::where([['active',1],['flujo',1]])->orderBy('name')->get(); 
			
			return view('factura.oficinaPartes.nomina',compact('documentos','tipos'));
			
		}
		else {
			return view('auth/login');
		}
	}
	
	
	/**
     * Funcion que Genera Número de Nómina para documentos seleccionados.
	 * Rol: oficinaPartes
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generarNomina(Request $request)
    {
		if (Auth::check()) {
			$nomina = time();
			$aux = 0;
			
			$checks = $request->check_list;
			
			if(!empty($checks)) {
				foreach($checks as $check) {
					$documento = Documento::find($check);
					
					if ($documento->archivo != null || $documento->tipoAcepta == 1) { //asigna nomina solo a documentos con archivo adjunto
						$documento->nomina = $nomina; 
						$documento->save();
					}
					else {
						$aux = $aux + 1;
					}
				}
			}
			if ($aux == 0) {
				return redirect('oficinaPartes/nomina')->with('message','nomina');
			}
			else {
				return redirect('oficinaPartes/nomina')->with('message','nominaWarning');
			}
		}
		else {
			return view('auth/login');
		}	
	}
	
	/**
     * Funcion que Envia documentos a Secretaria de Convenios.
	 * Rol: oficinaPartes
     *
     * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
     */
	public function enviaNomina(Request $request) 
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			$documentos = DB::table('documentos')
								->join('movimientos', 'documentos.id','=','movimientos.documento_id')
								->where([
										['documentos.establecimiento_id','=',$estab_user],
										['movimientos.active',1],
										['movimientos.estado','OP']
										])
								->whereNotNull('documentos.nomina')
								->select('documentos.id as id', 'movimientos.id as movimiento_id')->get();
				
			foreach($documentos as $documento) {
				//cambia estado de movimiento de activo a no activo
				$id = $documento->movimiento_id; 
				$movimiento = Movimiento::find($id);
				$movimiento->active = 0;
				
				$movimiento->save();

				//guarda nuevo movimiento
				$movimientoNew = new Movimiento;
				
				$movimientoNew->documento_id = $documento->id;
				$movimientoNew->estado = 'NP';
				$movimientoNew->observacion = $movimiento->observacion;
				$movimientoNew->user_id = Auth::user()->id;
					
				$movimientoNew->save();
			}
			
			return redirect('/oficinaPartes/nomina')->with('message','envioNomina');
		}	
		else {
			return view('auth/login');
		}
		
	}
	
	/**
     * Funcion que Lista las nominas generadas.
	 * Vista: factura.oficinaPartes.listaNomina
	 * Rol: oficinaPartes
     *
     * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
     */
	public function listaNomina(Request $request) 
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			$documentos = DB::table('documentos')
			              ->select('nomina')
						  ->where('documentos.establecimiento_id',$estab_user)
						  ->whereRaw('LENGTH(documentos.nomina) < 12 and LENGTH(documentos.nomina) > 9') /*muestra solo nominas de nueva version*/
						  ->whereNotNull('nomina')
						  ->groupBy('nomina')
						  ->orderBy('nomina', 'desc')->paginate(10); 
			
			return view('factura.oficinaPartes.listaNomina',compact('documentos'));
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Funcion que Genera impresión de nóminas.
	 * Vista: factura.oficinaPartes.printNomina
	 * Rol: oficinaPartes
     *
     * @param  \Illuminate\Http\Request  $request
	 * @param int $nomina Número de Nomina a Imprimir
	 * @return \Illuminate\Http\Response
     */
	public function printNomina(Request $request,$nomina) 
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			$documentos = DB::table('documentos')
							->join('proveedors','documentos.proveedor_id','=','proveedors.id')
							->join('tipo_docs','documentos.tipoDoc_id','=','tipo_docs.id')
							->select('proveedors.rut as rut',
									 'proveedors.name as nameProveedor',
									 'tipo_docs.name as tipoDoc',
									 'documentos.id as id',
									 'documentos.nDoc as nDoc',
									 'documentos.monto as monto',
									 'documentos.nomina as nomina')
							->selectRaw('DATE_FORMAT(documentos.fechaRecepcion, "%d-%m-%Y") as fechaRecepcion')
							->selectRaw('DATE_FORMAT(documentos.fechaEmision, "%d-%m-%Y") as fechaEmision')
							->where([['nomina',$nomina],
									 ['documentos.establecimiento_id','=',$estab_user]])
							->orderBy('documentos.id')->get();
			
			
			return view('factura.oficinaPartes.printNomina',compact('documentos'));
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
	 * Funcion que verifica si documento existe 
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param string $proveedor Rut Proveedor
	 * @param int $n_doc Numero de Documento
	 * @param string $tipo Tipo de Documento
     * @return \Illuminate\Http\Response
	 */
	public function docExiste(Request $request,$proveedor,$n_doc,$tipo) 
	{
		if($request->ajax()){
			
			//determina el ID del proveedor
			$explode      = explode("-",$proveedor);
			$rut          = $explode[0];
			$id_proveedor = Proveedor::where('rut',$rut)->first();
			
			//si proveedor no existe
			if ($id_proveedor != null && $n_doc != null && $tipo !=null ) {
				
				//tipo de documento
				$tipoExplode = explode("-",$tipo);
				$tipoValue   = $tipoExplode[0];
				
				$documento = Documento::where([['proveedor_id',$id_proveedor->id],['nDoc',$n_doc],['tipoDoc_id',$tipoValue]])->count();
				
				return response()->json($documento);
			}
			else {
				return 0; 
			}
		}
	} 
	
	
	/**
	 * Funcion que llama a formulario de ingreso de reporte ACEPTA
	 * Vista: factura.oficinaPartes.acepta
	 * Rol: oficinaPartes
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function ingresoAcepta(Request $request)
	{
		if (Auth::check()) {
			return view('factura.oficinaPartes.acepta');
		}
		else {
			return view('auth/login');
		}	
	}
	
	/**
	 * Funcion que Genera Accion de Cargar Documentos desde ACEPTA
	 * Vista: factura.oficinaPartes.respuestaAcepta
	 * Rol: oficinaPartes
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function uploadAcepta(Request $request)
	{			
		if (Auth::check()) { 
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;
			
			//verifica que archivo de Recepcionados Acepta existe
			if ($request->hasFile('archivo')) {
				$upload = request()->file('archivo');
				//dd($upload);
				if(!$upload->isValid()) {
					return redirect('oficinaPartes/acepta')
					->with('message','invalid')
					->withInput();
				}

				if($upload->getClientOriginalExtension() <> 'xls') {
					return redirect('oficinaPartes/acepta')
					->with('message','extension')
					->withInput();
				}
			}

			//verifica que archivo de NO Recepcionados Acepta existe
			if ($request->hasFile('archivo2')) {
				$upload = request()->file('archivo2');

				if(!$upload->isValid()) {
					return redirect('oficinaPartes/acepta')
					->with('message','invalid')
					->withInput();
				}

				if($upload->getClientOriginalExtension() <> 'xls') {
					return redirect('oficinaPartes/acepta')
					->with('message','extension')
					->withInput();
				}
			}
			
			//inicializa variables y contadores
			$respuestas = [];
			$cont   = 0;
			$error1 = 0;
			$error2 = 0;			
			$error3 = 0;
			$error4 = 0;
			$error5 = 0;

			//recorre documentos Recepcionados por ACEPTA
			if ($request->hasFile('archivo')) {
				$upload = request()->file('archivo');
				
				//carga archivo excel
				$rows = \Excel::load($upload, function($reader) {})->get();
				
				foreach ($rows as $key => $value) {

					/*Valida si archivo es correcto*/
					if (is_null($value->uri) || is_null($value->folio)) {
						return redirect('oficinaPartes/acepta')
						->with('message','archivo')
						->withInput();
					}
					
					/*Obtiene Numero de Documento*/
					$nDoc = $value->folio;
					/*Obtiene Monto*/
					$monto = $value->monto_total;
					
					/*Obtiene Proveedor*/
					$emisor = $value->emisor;
					$rut = explode("-", $emisor);
					$dv = $rut[1];
					$rut = str_ireplace('.','',$rut[0]);
					$rut = str_ireplace(' ','',$rut);
					
					$proveedor = Proveedor::where([['rut','=',$rut]])->first();
					
					/*Obtiene Tipo de Documento*/
					$tipo    = $value->tipo;
					
					//Si no es guia de despacho (52)
					if($tipo != 52) {
						$tipoDoc = TipoDoc::Where('id_sii','=',$tipo)->first();
						
						/*Obtiene Referencias*/
						$obj = json_decode($value->referencias, true);
						$ordenCompra = null;
						$docReferencia = null;
						if ( is_null($obj) == false ) {
							foreach( $obj as $k ) {
								//Orden de Compra
								if ( $k['Tipo'] == '801' ) { 
									$ordenCompra = substr($k['Folio'],0,15);
								}
								//Documento de Referencia
								if ( $k['Tipo'] == '34' ) {
									$docReferencia = $k['Folio'];
									if( is_int($docReferencia) == false || strlen ($docReferencia) > 11 ) {
										$docReferencia = null;
									}
								}
							}
						}	
						
						/*Verifica si proveedor existe*/
						if (is_null($proveedor)==false) {
							$proveedor_id   = $proveedor->id;
							$proveedor_name = $proveedor->name;
						}
						else {
							//imprimir documento no guardado
							$texto = $value->emisor.'::SIN DATO::'.$tipoDoc->name.'::'.$value->folio.'::Proveedor no Existe::2';
							array_push($respuestas, $texto);
							$error2 = $error2 + 1;
							continue;
						}
						
						/*Verifica si tipo de documento existe*/
						if (is_null($tipoDoc)) {
							//imprimir documento no guardado
							$texto = $value->emisor.'::'.$proveedor_name.'::SIN DATO::'.$value->folio.'::Tipo de Documento no Existe::1';
							array_push($respuestas, $texto);
							$error1 = $error1 + 1;
							continue;
						}
						else {
							$tipoDoc_id   = $tipoDoc->id;
						}
						
						/*Verifica si documento existe*/
						$documentoCount = Documento::where([['proveedor_id',$proveedor_id],['nDoc',$nDoc],['tipoDoc_id',$tipoDoc_id]])->count();
						if($documentoCount > 0) {
							//imprimir documento no guardado
							$texto = $value->emisor.'::'.$proveedor_name.'::'.$tipoDoc->name.'::'.$value->folio.'::Documento Duplicado::3';
							array_push($respuestas, $texto);
							$error3 = $error3 + 1;
							continue;
						}
						
						/*Obtiene fecha de emisión*/
						$fechaEmision = '';
						if (is_null($value->emision)==FALSE) {
							$fechaEmision = $value->emision;
						} else {
							//imprimir documento no guardado
							$texto = $value->emisor.'::'.$proveedor_name.'::'.$tipoDoc->name.'::'.$value->folio.'::Fecha de Emisión no Existe::4';
							array_push($respuestas, $texto);
							$error4 = $error4 + 1;
							continue;
						}
						
						/*Obtiene Fecha Recepción y  Fecha de Vencimiento   */
						$fechaRecepcion = date('Y-m-d H:i:s');
						$fechaVencimiento = date('Y-m-d H:i:s',strtotime('+45 day',strtotime($fechaRecepcion)));
						
						/*Obtiene Archivo de Sitio Acepta*/
						$archivoName = null;
						try {
							/*Valida si archivo es correcto*/
							if (is_null($value->uri)) { 
								return redirect('oficinaPartes/acepta')
								->with('message','archivo')
								->withInput();
							}
							
							$uri    = $value->uri;
							$array  = explode("/",$uri);
							
							// throw new Exception('Existio un error, provocado',0);
							$enlace = $array[0].'/'.$array[1].'/'.$array[2].'/';
							$result = $enlace.'ca4webv3/PdfViewMedia?url='.$enlace.'/'.$array[3].'/'.$array[4];
							//dd($result); /// <--------------------------------------------------------------------------------------------------------------
							//$archivoName = 'd'.time().'_'.$proveedor_name.'_'.$nDoc.'.pdf';
							$archivoName = $value->uri;

							$path=$result;
							
							$archivo = file_get_contents($path);

							
							/*valida que archivo no esté corrupto*/
							/*
							$endfile = strpos($archivo,"%%EOF");
							if ($endfile === false){
								//imprimir documento no guardado
								$texto = $value->emisor.'::'.$proveedor_name.'::'.$tipoDoc->name.'::'.$value->folio.'::Error al Adjuntar Documento - Repetir Carga::5';
								array_push($respuestas, $texto);
								$error5 = $error5 + 1;
								continue;
							}
							*/
							/*
							$file = Response::make($archivo , 200, [
								'Content-Type' => 'application/pdf',
								'Content-Disposition' => 'inline; filename="'.$archivoName.'"'
							]);
							*/
							/*
							if(strpos($archivo,'<pdf:PDFVersion>')){
								
								Storage::put('public/'.$archivoName, $file);
							}
							else {
								
								//imprimir documento no guardado
								$texto = $value->emisor.'::'.$proveedor_name.'::'.$tipoDoc->name.'::'.$value->folio.'::Error al Adjuntar Documento::5';
								array_push($respuestas, $texto);
								$error5 = $error5 + 1;
								continue;
							}
							*/

						}
						catch(Exception $e){
							//imprimir documento no guardado
							$texto = $value->emisor.'::'.$proveedor_name.'::'.$tipoDoc->name.'::'.$value->folio.'::Error al Adjuntar Documento::5';
							array_push($respuestas, $texto);
							$error5 = $error5 + 1;
							continue;
						}
						

						
						/*guarda documento en DB*/
						$documento = new Documento;
						
						$documento->proveedor_id       = $proveedor_id;
						$documento->tipoDoc_id         = $tipoDoc_id;
						$documento->establecimiento_id = $estab_user;
						$documento->nDoc               = $nDoc;
						$documento->ordenCompra		   = $ordenCompra;	
						$documento->facAsociada		   = $docReferencia;
						$documento->fechaEmision       = $fechaEmision;
						$documento->fechaRecepcion     = $fechaRecepcion;
						$documento->fechaVencimiento   = $fechaVencimiento;
						$documento->monto              = $monto;
						$documento->user_id = Auth::user()->id;
						if($archivoName != null) {
							//$documento->archivo = Storage::url($archivoName);
							$documento->archivo = $archivoName;
						}
						
						$documento->save();
						
						//guarda movimiento
						if ( $documento->id != null ) {
							$movimiento = new Movimiento;
							
							$movimiento->documento_id = $documento->id;
							$movimiento->estado = 'OP';
							$movimiento->observacion = 'Carga de Datos - ACEPTA';
							$movimiento->user_id = Auth::user()->id;
							
							$movimiento->save();
						}
						
						$cont = $cont + 1;
					}
				}
			}
			
			//recorre documentos No Recepcionados por ACEPTA
			if ($request->hasFile('archivo2')) {
				$upload = request()->file('archivo2');
				
				//carga archivo excel
				$rows = \Excel::load($upload, function($reader) {})->get();
				
				foreach ($rows as $key => $value) {
					/*Valida si archivo es correcto*/
					if (!is_null($value->uri) || is_null($value->folio)) {
						return redirect('oficinaPartes/acepta')
						->with('message','archivo')
						->withInput();
					}
					
					/*Obtiene Numero de Documento*/
					$nDoc = $value->folio;
					
					/*Obtiene Monto*/
					$monto = $value->monto_total;
					
					/*Obtiene Tipo de Documento*/
					$tipo    = $value->tipo;
					$tipoDoc = TipoDoc::Where('id_sii','=',$tipo)->first();
					
					/*Obtiene Proveedor*/
					$emisor = $value->emisor;
					$rut = explode("-", $emisor);
					$dv = $rut[1];
					$rut = str_ireplace('.','',$rut[0]);
					$rut = str_ireplace(' ','',$rut);
					
					$proveedor = Proveedor::where([['rut','=',$rut]])->first();
					
					/*Verifica si proveedor existe*/
					if (is_null($proveedor)==false) {
						$proveedor_id   = $proveedor->id;
						$proveedor_name = $proveedor->name;
					}
					else {
						//imprimir documento no guardado
						$texto = $value->emisor.'::SIN DATO::'.$tipoDoc->name.'::'.$value->folio.'::Proveedor no Existe::2';
						array_push($respuestas, $texto);
						$error2 = $error2 + 1;
						continue;
					}
					
					/*Verifica si tipo de documento existe*/
					if (is_null($tipoDoc)) {
						//imprimir documento no guardado
						$texto = $value->emisor.'::'.$proveedor_name.'::SIN DATO::'.$value->folio.'::Tipo de Documento no Existe::1';
						array_push($respuestas, $texto);
						$error1 = $error1 + 1;
						continue;
					}
					else {
						$tipoDoc_id   = $tipoDoc->id;
					}
					
					/*Verifica si documento existe*/
					$documentoCount = Documento::where([['proveedor_id',$proveedor_id],['nDoc',$nDoc],['tipoDoc_id',$tipoDoc_id]])->count();
					if($documentoCount > 0) {
						//imprimir documento no guardado
						$texto = $value->emisor.'::'.$proveedor_name.'::'.$tipoDoc->name.'::'.$value->folio.'::Documento Duplicado::3';
						array_push($respuestas, $texto);
						$error3 = $error3 + 1;
						continue;
					}
					
					/*Obtiene fecha de emisión*/
					$fechaEmision = '';
					if (is_null($value->emision)==FALSE) {
						$fechaEmision = $value->emision;
					} else {
						//imprimir documento no guardado
						$texto = $value->emisor.'::'.$proveedor_name.'::'.$tipoDoc->name.'::'.$value->folio.'::'.$value->folio.'::Fecha de Emisión no Existe::4';
						array_push($respuestas, $texto);
						$error4 = $error4 + 1;
						continue;
					}
					
					/*Obtiene Fecha Recepción y  Fecha de Vencimiento   */
					$fechaRecepcion = date('Y-m-d H:i:s');
					$fechaVencimiento = date('Y-m-d H:i:s',strtotime('+45 day',strtotime($fechaRecepcion)));
					
					/*guarda documento en DB*/
					$documento = new Documento;
					
					$documento->proveedor_id       = $proveedor_id;
					$documento->tipoDoc_id         = $tipoDoc_id;
					$documento->establecimiento_id = $estab_user;
					$documento->nDoc               = $nDoc;
					$documento->fechaEmision       = $fechaEmision;
					$documento->fechaRecepcion     = $fechaRecepcion;
					$documento->fechaVencimiento   = $fechaVencimiento;
					$documento->monto              = $monto;
					$documento->tipoAcepta         = 1;
					$documento->user_id = Auth::user()->id;
					
					$documento->save();
					
					//guarda movimiento
					if ( $documento->id != null ) {
						$movimiento = new Movimiento;
						
						$movimiento->documento_id = $documento->id;
						$movimiento->estado = 'OP';
						$movimiento->observacion = 'Carga de Datos - ACEPTA';
						$movimiento->user_id = Auth::user()->id;
						
						$movimiento->save();
					}
					
					$cont = $cont + 1;
				}	
			}
			return view('factura.oficinaPartes.respuestaAcepta',compact('respuestas','cont','error1','error2','error3','error4','error5'));
		}
		else {
			return view('auth/login');
		}	
	}
	
    /*******************************************************************************************/

	/*                             SECRETARIA DE CONVENIOS                                     */
	/*******************************************************************************************/
	/**
     * Funcion que Lista los documentos que se encuentran en secretaria de Convenios.
	 * Vista: factura.secretariaConvenios.index
	 * Rol: secretariaConvenios
     *
     * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
     */
    public function secretariaConvenios(Request $request)
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;

			//guarda en sesion los filtros de documentos
			if(Session::get('message') == null) {
				Session::forget('searchRut'); //Elimina la variable en session
				Session::forget('searchTipo'); //Elimina la variable en session
				Session::forget('searchNdoc'); //Elimina la variable en session
				
				Session::put('searchRut', $request->get('searchRut'));
				Session::put('searchTipo', $request->get('searchTipo'));
				Session::put('searchNdoc', $request->get('searchNdoc'));
			}
			
			$documentos =  DB::table('documentos')
							->join('movimientos', 'documentos.id','=','movimientos.documento_id')
							->join('proveedors','documentos.proveedor_id','=','proveedors.id')
							->join('tipo_docs','documentos.tipoDoc_id','=','tipo_docs.id')
							->select('proveedors.rut as rut',
									 'proveedors.name as nameProveedor',
									 'tipo_docs.name as tipoDoc',
									 'documentos.id as id',
									 'documentos.nDoc as nDoc',
									 'documentos.ordenCompra as ordenCompra',
									 'documentos.archivo as archivo',
									 'movimientos.observacion as observacion',
									 'documentos.nomina as nomina')
							->selectRaw('DATE_FORMAT(documentos.fechaRecepcion, "%d-%m-%Y") as fechaRecepcion')
							->where([
									['documentos.establecimiento_id','=',$estab_user],
									['movimientos.active',1],
									['movimientos.estado','NP'],
									['rut', 'LIKE', '%'.Session::get('searchRut').'%'],
									['tipo_docs.id', 'LIKE', '%'.Session::get('searchTipo').'%'],
									['nDoc', 'LIKE', '%'.Session::get('searchNdoc').'%'],
									])
							->orderBy('documentos.id','desc')->paginate(10)
							->appends('searchRut',Session::get('searchRut'))
							->appends('searchTipo',Session::get('searchTipo'))
							->appends('searchNdoc',Session::get('searchNdoc'));
			
			$tipos = TipoDoc::where([['active',1],['flujo',1]])->orderBy('name')->get();
			
			return view('factura.secretariaConvenios.index',compact('documentos','tipos'));
		}	
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Funcion que Envia documentos a Oficina de Convenios.
	 * Rol: secretariaConvenios
     *
     * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
     */
	public function enviaSecretariaConvenios(Request $request) 
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;
			
			$aux = 0;
			
			$checks = $request->check_list;
			
			if(!empty($checks)) {
				foreach($checks as $check) {
					$documento = Documento::find($check);
					
					$tipoDoc = TipoDoc::find($documento->tipoDoc_id);
					
					if ($documento->ordenCompra != null || $tipoDoc->oc == 0) { //envia documentos solo con número de orden de compra si se requiere
							
						if ($documento->archivo != null) { //si posee documento adjunto		
							$mov = Movimiento::where([['documento_id',$check],['active',1]])->first();
							
							//cambia estado de movimiento de activo a no activo
							$id = $mov->id; 
							$movimiento = Movimiento::find($id);
							$movimiento->active = 0;
							
							$movimiento->save();
							
							//busca convenios asociados al proveedor (cantidad)
							$convenioCount = Convenio::where([['proveedor_id',$documento->proveedor_id],['establecimiento_id',$estab_user],['active',1]])->count();
							
							if($convenioCount == 1) { //si el proveedor esta asociado a un único convenio activo
								//asigna convenio a documento
								$convenio = Convenio::where([['proveedor_id',$documento->proveedor_id],['establecimiento_id',$estab_user],['active',1]])->first();
								$documento->convenio_id = $convenio->id; 
								$documento->save();
								
								$movimientoNew = new Movimiento;
								
								$movimientoNew->documento_id = $check;
								$movimientoNew->estado = 'VB';
								$movimientoNew->user_id = Auth::user()->id;
									
								$movimientoNew->save();
								
								//envia mail
								try {
									$this->enviaMail( $convenio->id, $documento->id, $estab_user );
								}
								catch(Exception $e){}
							}
							else {
								//guarda nuevo movimiento
								$movimientoNew = new Movimiento;
								
								$movimientoNew->documento_id = $check;
								$movimientoNew->estado = 'CV';
								$movimientoNew->user_id = Auth::user()->id;
									
								$movimientoNew->save();
							}	
							
							//almacena datos en ABEX
							$proveedor = Proveedor::find($documento->proveedor_id);
							
							try {
								$oracle = new OracleDocumento;
								
								$oracle->id = $documento->id;
								$oracle->proveedor_rut = $proveedor->rut."-".$proveedor->dv;
								$oracle->proveedor_nombre = $proveedor->name;
								$oracle->tipo_doc_id = $documento->tipoDoc_id;
								$oracle->num_doc = $documento->nDoc;
								$oracle->fecha_emision = $documento->fechaEmision;
								$oracle->monto = $documento->monto;
								$oracle->orden_compra = $documento->ordenCompra;
								
								$oracle->fecha_creado = $documento->created_at;
								$oracle->fecha_modificado = $documento->updated_at;
								if($convenioCount == 1) {
									$oracle->estado = 'VB';
									$oracle->estado_nombre = 'Referente Tecnico - Por Validar';
								}
								else {
									$oracle->estado = 'CV';
									$oracle->estado_nombre = 'En Convenios - Asignar a Referente Tecnico';
								}
								$oracle->save();
							}
							catch(Exception $e) {}
							//fin almacena datos en ABEX
						}
						else {
							$aux = $aux + 1;
						}
					}
					else {
						$aux = $aux + 1;
					}
					
				}
			}
			if ($aux == 0) {
				return redirect('secretariaConvenios')->with('message','envio');
			}
			else {
				return redirect('secretariaConvenios')->with('message','envioWarning');
			}			
		}	
		else {
			return view('auth/login');
		}
	}
	
	/*******************************************************************************************/
	/*                                 OFICINA DE CONVENIOS                                    */
	/*******************************************************************************************/
	/**
     * Funcion que Lista los documentos que se encuentran en convenios.
	 * Vista: factura.oficinaConvenios.index
	 * Rol: convenios
     *
     * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
     */
	public function convenios(Request $request)
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			$documentos =  DB::table('documentos')
							->join('movimientos', 'documentos.id','=','movimientos.documento_id')
							->join('proveedors','documentos.proveedor_id','=','proveedors.id')
							->join('tipo_docs','documentos.tipoDoc_id','=','tipo_docs.id')
							->select('proveedors.rut as rut',
									 'proveedors.name as nameProveedor',
									 'tipo_docs.name as tipoDoc',
									 'documentos.id as id',
									 'documentos.nDoc as nDoc',
									 'documentos.monto as monto',
									 'documentos.ordenCompra as ordenCompra',
									 'documentos.archivo as archivo',
									 'movimientos.estado as estado',
									 'movimientos.observacion as observacion')
							->selectRaw('DATE_FORMAT(documentos.fechaRecepcion, "%d-%m-%Y") as fechaRecepcion')
							->where([
									['documentos.establecimiento_id','=',$estab_user],
									['movimientos.active',1],
									['rut', 'LIKE', '%'.$request->get('searchRut').'%'],
									['tipo_docs.id', 'LIKE', '%'.$request->get('searchTipo').'%'],
									['nDoc', 'LIKE', '%'.$request->get('searchNdoc').'%'],
									['estado', 'LIKE', '%'.$request->get('searchEstado').'%'],
									])
							->whereIn('movimientos.estado',['CV','DV'])		
							->orderBy('documentos.id','desc')->paginate(10)
							->appends('searchRut',$request->get('searchRut'))
							->appends('searchTipo',$request->get('searchTipo'))
							->appends('searchNdoc',$request->get('searchNdoc'))
							->appends('searchEstado',$request->get('searchEstado'));
			
			$tipos = TipoDoc::where([['active',1],['flujo',1]])->orderBy('name')->get();
			
			//parametros de filtro
			$searchRut    = $request->get('searchRut');
			$searchTipo   = $request->get('searchTipo');
			$searchNdoc   = $request->get('searchNdoc');
			$searchEstado = $request->get('searchEstado');
			$page         = $request->get('page');
			
			return view('factura.oficinaConvenios.index',compact('documentos','tipos','searchRut','searchTipo','searchNdoc','searchEstado','page'));
		}	
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Funcion que Envia Documentos a Referente Tecnico.
	 * Vista: factura.oficinaConvenios.envioVB
	 * Rol: convenios
     *
     * @param  \Illuminate\Http\Request  $request
	 * @param int $id Id Documento
	 * @return \Illuminate\Http\Response
     */
	public function envioVB(Request $request, $id)
	{
		if (Auth::check()) {
			$documento = Documento::find($id);
			
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;
			
			//determina si el documento se encuentra en el estado correcto
			$aux = 0; 
			$aux = Movimiento::where([['active',1],['documento_id',$id]])->whereIn('estado',['CV','DV'])->count();
			
			if ( $documento->establecimiento_id == $estab_user & $aux == 1 ) {	
				
				//determina los convenios asociados al proveedor
				$convenios = DB::table('convenios')
							->join('referentes', 'referentes.id','=','convenios.referente_id')
							->select('convenios.id as id',
									 'convenios.identificador as identificador',
									 'referentes.name as referente_name')
							->where([['convenios.proveedor_id',$documento->proveedor_id],['convenios.active',1],['convenios.establecimiento_id',$estab_user]])
							->orderBy('convenios.identificador')->get();		 
				
				$proveedor = Proveedor::find($documento->proveedor_id);
				$tipo = TipoDoc::find($documento->tipoDoc_id);
				
				//parametros de filtro
				$searchRut    = $request->get('searchRut');
				$searchTipo   = $request->get('searchTipo');
				$searchNdoc   = $request->get('searchNdoc');
				$searchEstado = $request->get('searchEstado');
				$page         = $request->get('page'); 
			
				return view('factura.oficinaConvenios.envioVB',compact('documento','proveedor','tipo','convenios','searchRut','searchTipo','searchNdoc','searchEstado','page'));
			}
			else {
				return view('home');
			}
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Funcion que Asigna convenio a documento a Referente Técnico.
	 * Rol: convenios
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function asignaConvenio(Request $request)
    {
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			//asigna convenio	
			$documento = Documento::find($request->documento_id);
		
			$documento->convenio_id = $request->convenio; 
			$documento->save();
			
			//cambia estado de movimiento de activo a no activo
			$mov = Movimiento::where([['documento_id',$request->documento_id],['active',1]])->first();
			
			$id = $mov->id; 
			$movimiento = Movimiento::find($id);
			$movimiento->active = 0;
			
			$movimiento->save();
			
			//guarda nuevo movimiento
			$movimientoNew = new Movimiento;
			
			$movimientoNew->documento_id = $request->documento_id;
			$movimientoNew->estado = 'VB';
			$movimientoNew->user_id = Auth::user()->id;
				
			$movimientoNew->save();
			
			//envia mail
			try {
				$this->enviaMail( $request->convenio, $request->documento_id, $estab_user );
			}
			catch(Exception $e){}
			
			//almacena datos en ABEX
			try {
				$result = DB::connection('oracle')->update("UPDATE DOCUMENTOS SET ESTADO = 'VB', ESTADO_NOMBRE = 'Referente Tecnico - Por Validar', FECHA_MODIFICADO = '".$movimientoNew->created_at."' WHERE ID = ".$movimientoNew->documento_id);	
			}
			catch(Exception $e){}
			//fin datos en ABEX 
			
			//parametros de filtro
			$searchRut    = $request->get('searchRut');
			$searchTipo   = $request->get('searchTipo');
			$searchNdoc   = $request->get('searchNdoc');
			$searchEstado = $request->get('searchEstado');	
			$page         = $request->get('page');
			
			return redirect('oficinaConvenios/vistosBuenos?searchNdoc='.$searchNdoc.'&searchRut='.$searchRut.'&searchTipo='.$searchTipo.'&searchEstado='.$searchEstado.'&page='.$page)->with('message','envio');
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Funcion que Envia notificaciones via email a los referentes técnicos 
     *
     * @param int $convenio_id Identificación de convenio
	 * @param int $docuento_id Identificación de documento
	 * @param int $establecimiento_id Identificación de establecimiento
     * @return \Illuminate\Http\Response
     */
	private function enviaMail($convenio_id, $documento_id, $establecimiento_id) 
	{
		//selecciona convenio
		$convenio = Convenio::find($convenio_id);
		
		//selecciona documento
		$documento = Documento::find($documento_id);
		
		//seleccion proveedor
		$proveedor = Proveedor::find($documento->proveedor_id);
		
		//selecciona establecimiento
		$establecimiento = Establecimiento::find($establecimiento_id);
		
		//determina usuarios pertenecientes al departamento definido en el convenio
		$users = DB::table('referente_user')
				   ->where('referente_id','=',$convenio->referente_id)->get();
		
		foreach ($users as $user) {
			//selecciona usuario
			$aux = User::find($user->user_id);
			
			//si usuario esta activo y es referente  tecnico, envía email
			if( $aux->active == 1 && $aux->isRole('Referente Tecnico') && $aux->isEstab($establecimiento->name)  ) {
				$aux->notify(new ReferenteMail($proveedor->name, $documento->nDoc));
			}
			
		}

	}
	
	/**
     * Funcion que Lista los documentos que se encuentran en Convenios, ya Validados por el Referente Técnico.
	 * Vista: factura.oficinaConvenios.validados
	 * Rol: convenios
     *
     * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
     */
	public function validados(Request $request)
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			$documentos =  DB::table('documentos')
							->join('movimientos', 'documentos.id','=','movimientos.documento_id')
							->join('proveedors','documentos.proveedor_id','=','proveedors.id')
							->join('tipo_docs','documentos.tipoDoc_id','=','tipo_docs.id')
							->join('convenios','documentos.convenio_id','=','convenios.id')
							->join('referentes','convenios.referente_id','=','referentes.id')
							->select('proveedors.rut as rut',
									 'proveedors.name as nameProveedor',
									 'tipo_docs.name as tipoDoc',
									 'documentos.id as id',
									 'documentos.nDoc as nDoc',
									 'documentos.monto as monto',
									 'referentes.name as referente',
									 'documentos.ordenCompra as ordenCompra',
									 'documentos.archivo as archivo',
									 'movimientos.estado as estado',
									 'movimientos.observacion as observacion')
							->selectRaw('DATE_FORMAT(documentos.fechaRecepcion, "%d-%m-%Y") as fechaRecepcion')
							->selectRaw('DATE_FORMAT(movimientos.created_at, "%d-%m-%Y") as fechaValidacion')
							->where([
									['documentos.establecimiento_id','=',$estab_user],
									['movimientos.active',1],
									['rut', 'LIKE', '%'.$request->get('searchRut').'%'],
									['tipo_docs.id', 'LIKE', '%'.$request->get('searchTipo').'%'],
									['convenios.referente_id', 'LIKE', $request->get('searchReferente')],
									['nDoc', 'LIKE', '%'.$request->get('searchNdoc').'%'],
									])
							->whereIn('movimientos.estado',['RC','DC'])		
							->orderBy('documentos.id','desc')->paginate(10)
							->appends('searchRut',$request->get('searchRut'))
							->appends('searchTipo',$request->get('searchTipo'))
							->appends('searchNdoc',$request->get('searchNdoc'))
							->appends('searchReferente',$request->get('searchReferente'));
			
			$tipos      = TipoDoc::where([['active',1],['flujo',1]])->orderBy('name')->get();
			$referentes  = Referente::where([['establecimiento_id',$estab_user],['active',1]])->orderBy('name')->get();
			
			return view('factura.oficinaConvenios.validados',compact('documentos','tipos','referentes'));
		}	
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Funcion que llama a pantalla de devolución los documentos a referente tecnico en caso de observacion.
	 * Vista: factura.oficinaConvenios.devolver
	 * Rol: convenios
     *
     * @param  \Illuminate\Http\Request  $request
	 * @param int $id Identificación de documento
	 * @return \Illuminate\Http\Response
     */
    public function devolverReferente2(Request $request,$id)
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			$documento = Documento::find($id);
			
			if ( $documento->establecimiento_id == $estab_user ) {	
				
				$proveedor = Proveedor::find($documento->proveedor_id);
				
				$tipo = TipoDoc::find($documento->tipoDoc_id);
				
				return view('factura.oficinaConvenios.devolver',compact('documento','proveedor','tipo'));
			}
			else {
				return view('home');
			}
		}
		else {
			return view('auth/login');
		}
	}

	/**
     * Funcion que Devuelve documentos de Convenios a Referente Técnico
	 * Rol: convenios
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function movDevolverRt2(Request $request) 
	{
		if (Auth::check()) {
			
			$mov = Movimiento::where([['documento_id',$request->documento_id],['active',1]])->first();
			
			//cambia estado de movimiento de activo a no activo
			$id = $mov->id; 
			$movimiento = Movimiento::find($id);
			$movimiento->active = 0;
			
			$movimiento->save();
			
			//guarda nuevo movimiento
			$movimientoNew = new Movimiento;
			
			$movimientoNew->documento_id = $request->documento_id;
			$movimientoNew->estado = 'DO';
			$movimientoNew->observacion = str_replace(PHP_EOL,"<br>",$request->observacion);
			$movimientoNew->user_id = Auth::user()->id;
				
			$movimientoNew->save();
			
			//almacena datos en ABEX
			try {
				$result = DB::connection('oracle')->update("UPDATE DOCUMENTOS SET ESTADO = 'DO', ESTADO_NOMBRE = 'Devuelto Convenios - Referente Tecnico', FECHA_MODIFICADO = '".$movimientoNew->created_at."' WHERE ID = ".$movimientoNew->documento_id);	
			}
			catch(Exception $e){}
			//fin datos en ABEX
			
			return redirect('oficinaConvenios/validados')->with('message','devolver');
			
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Funcion que Envia documentos a Contabilidad
	 * Rol: convenios
     *
     * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
     */
	public function envioContabilidad(Request $request)
	{
		if (Auth::check()) {
			$checks = $request->check_list;
			
			if(!empty($checks)) {
				foreach($checks as $check) {
					$documento = Documento::find($check);
					
					$mov = Movimiento::where([['documento_id',$check],['active',1]])->first();
					
					//cambia estado de movimiento de activo a no activo
					$id = $mov->id; 
					$movimiento = Movimiento::find($id);
					$movimiento->active = 0;
					
					$movimiento->save();
					
					//guarda nuevo movimiento
					$movimientoNew = new Movimiento;
					
					$movimientoNew->documento_id = $check;
					$movimientoNew->estado = 'DE';
					$movimientoNew->user_id = Auth::user()->id;
						
					$movimientoNew->save();
					
					//almacena datos en ABEX
					try {
						$result = DB::connection('oracle')->update("UPDATE DOCUMENTOS SET ESTADO = 'DE', ESTADO_NOMBRE = 'En Contabilidad', FECHA_MODIFICADO = '".$movimientoNew->created_at."' WHERE ID = ".$movimientoNew->documento_id);	
					}
					catch(Exception $e){}
					//fin datos en ABEX
				}
				
			}	
			return redirect('oficinaConvenios/validados')->with('message','envio');
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Funcion que Lista documentos aprobados por el referente técnico a los cuales se les puede adjuntar más validadores 
	 * Vista: factura.oficinaConvenios.adjuntar
	 * Rol: convenios
     *
     * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
     */
	public function adjuntarValidadores2(Request $request)
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;
			
			$documentos =  DB::table('documentos')
							->join('movimientos', 'documentos.id','=','movimientos.documento_id')
							->join('proveedors','documentos.proveedor_id','=','proveedors.id')
							->join('tipo_docs','documentos.tipoDoc_id','=','tipo_docs.id')
							->join('convenios','documentos.convenio_id','=','convenios.id')
							->join('referentes','convenios.referente_id','=','referentes.id')
							->select('proveedors.rut as rut',
									 'proveedors.name as nameProveedor',
									 'tipo_docs.name as tipoDoc',
									 'documentos.id as id',
									 'documentos.nDoc as nDoc',
									 'documentos.monto as monto',
									 'referentes.name as referente',
									 'documentos.ordenCompra as ordenCompra',
									 'documentos.archivo as archivo',
									 'movimientos.estado as estado',
									 'movimientos.observacion as observacion')
							->selectRaw('DATE_FORMAT(documentos.fechaRecepcion, "%d-%m-%Y") as fechaRecepcion')
							->where([
									['documentos.establecimiento_id','=',$estab_user],
									['movimientos.active',1],
									['rut', 'LIKE', '%'.$request->get('searchRut').'%'],
									['tipo_docs.id', 'LIKE', '%'.$request->get('searchTipo').'%'],
									['nDoc', 'LIKE', '%'.$request->get('searchNdoc').'%'],
									])
							->whereIn('movimientos.estado',['DE','RC','RT','VB'])		
							->orderBy('documentos.id','desc')->paginate(10)
							->appends('searchRut',$request->get('searchRut'))
							->appends('searchTipo',$request->get('searchTipo'))
							->appends('searchNdoc',$request->get('searchNdoc'));
			
			$tipos = TipoDoc::where([['active',1],['flujo',1]])->orderBy('name')->get();
			
			return view('factura.oficinaConvenios.adjuntar',compact('documentos','tipos'));
		}	
		else {
			return view('auth/login');
		}	
	}

	/**
     * Funcion que adjunta Validadores para perfil de convenios
	 * Vista: factura.oficinaConvenios.formularioAdjuntar
	 * Rol: convenios
     *
	 * @param  \Illuminate\Http\Request  $request
	 * @param int $id Identificación de documento
	 * @return \Illuminate\Http\Response
	 */	 
	public function formularioAdjuntar(Request $request, $id) 
	{	
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;
			
			$documento = Documento::find($id);
			
			if ( $documento->establecimiento_id == $estab_user ) {	
				
				$proveedor  = Proveedor::find($documento->proveedor_id);
				$tipo       = TipoDoc::find($documento->tipoDoc_id);
				$validadors = Validador::where('active',1)->orderBy('name')->get(); 
				
				$validadores = DB::table('validadors')
							   ->join('documento_validadors', 'validadors.id','documento_validadors.validador_id')
							   ->where([['documento_validadors.documento_id','=',$id],
										['documento_validadors.active','=','1']])
							   ->select('validadors.name as name',
										'validadors.id as validador_id',
										'documento_validadors.archivo as archivo')
							   ->orderBy('validadors.name')->get();
				
				return view('factura.oficinaConvenios.formularioAdjuntar',compact('documento','proveedor','tipo','validadores','validadors'));
			}
			
		}	
		else {
			return view('auth/login');
		}
	}

	/**
     * Accion de Adjuntar Validadores para perfil de convenios
	 * Rol: convenios
     *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */		
	public function accionAdjuntar(Request $request) 
	{
		if (Auth::check()) {
			$documento = Documento::find($request->documento_id);
			
			$validadores = DB::table('documento_validadors')
							   ->where('documento_id',$documento->id)
							   ->select('validador_id as id')->get();
							   
			//valida modificaciones de documentos adjuntos realizadas por convenios
			foreach ($validadores as $validador)
			{
				if ( $request->hasFile('f'.$validador->id) ) { 
					$validator = validator::make($request->all(), [
						'f'.$validador->id => "nullable|mimes:pdf|max:10024",
					]);
					
					if ($validator->fails()) {
						return redirect('oficinaConvenios/'.$request->documento_id.'/adjuntar')
								->withErrors($validator)
								->withInput();
					}
				}
			}
			//valida documentos adjuntos nuevos realizados por convenios
			if ( $request->hasFile('otro') ) { 
				$validator = validator::make($request->all(), [
					'otro' => 'nullable|mimes:pdf|max:10024',
					'tipo' => 'required',
				]);
				
				if ($validator->fails()) {
					return redirect('oficinaConvenios/'.$request->documento_id.'/adjuntar')
							->withErrors($validator)
							->withInput();
				}
			}
			
			//adjunta doucumentos modificados
			foreach ($validadores as $validador) 
			{
				//adjunta archivo
				$archivoName = null;
				$dv_old      = null;
				
				if ($request->hasFile('f'.$validador->id)) {
					
					$archivo = $request->file('f'.$validador->id);
					$archivoName = 'v'.time().$archivo->getClientOriginalName();
					
					//sube archivo
					$request->file('f'.$validador->id)->storeAs('public',$archivoName);
					
					//desactiva documento si existe anterior
					$dv_old = DocumentoValidador::where([['documento_id',$request->documento_id],['validador_id',$validador->id],['active','1']])->first();
					
					if ($dv_old != null ) {
						$aux = DocumentoValidador::find($dv_old->id);
						$aux->active = 0;
						$aux->save();
					}
					
					//guarda nombre de archivo
					$dv = new DocumentoValidador;
					
					$dv->documento_id = $request->documento_id;
					$dv->validador_id = $validador->id;
					$dv->archivo      = Storage::url($archivoName);
					$dv->active       = 1;
					
					$dv->save();
				}
			}
			
			//adjunta documento nuevo
			if ( $request->hasFile('otro') ) {
				$archivo = $request->file('otro');
				$archivoName = 'v'.time().$archivo->getClientOriginalName();
				
				//sube archivo
				$request->file('otro')->storeAs('public',$archivoName);
				
				//guarda nombre de archivo
				$dv = new DocumentoValidador;
				
				$dv->documento_id = $request->documento_id;
				$dv->validador_id = $request->tipo;
				$dv->archivo      = Storage::url($archivoName);
				$dv->active       = 1;
				
				$dv->save(); 
			}

			return redirect('oficinaConvenios/documentosAdjuntar')->with('message','adjuntar');	
			
		}	
		else {
			return view('auth/login');
		}	
	}
	
	
	/*******************************************************************************************/
	/*                                   REFERENTE TECNICO                                     */
	/*******************************************************************************************/
	/**
     * Funcion que Lista los documentos que se encuentran en el referente tecnico.
	 * Vista: factura.referenteTecnico.index
	 * Rol: referenteTecnico
     *
     * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
     */
    public function referenteTecnico(Request $request)
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;	

			//guarda en sesion los filtros de documentos
			if(Session::get('message') == null) {
				Session::forget('searchRut'); //Elimina la variable en session
				Session::forget('searchTipo'); //Elimina la variable en session
				Session::forget('searchNdoc'); //Elimina la variable en session
				Session::forget('searchAdjunto'); //Elimina la variable en session
				
				Session::put('searchRut', $request->get('searchRut'));
				Session::put('searchTipo', $request->get('searchTipo'));
				Session::put('searchNdoc', $request->get('searchNdoc'));
				Session::put('searchAdjunto', $request->get('searchAdjunto'));
			}			
			
			$documentos =  DB::table('documentos')
							->join('movimientos', 'documentos.id','=','movimientos.documento_id')
							->join('proveedors','documentos.proveedor_id','=','proveedors.id')
							->join('tipo_docs','documentos.tipoDoc_id','=','tipo_docs.id')
							->join('convenios','documentos.convenio_id','=','convenios.id')
							->join('referentes','convenios.referente_id','=','referentes.id')
							->join('referente_user','referentes.id','=','referente_user.referente_id')							
							->leftJoin('documento_validadors', function ($join) {
									$join->on('documentos.id','=','documento_validadors.documento_id')
									->where('documento_validadors.active','<>','0');
								})
							->select('proveedors.rut as rut',
									'proveedors.name as nameProveedor',
									'tipo_docs.name as tipoDoc',
									'documentos.id as id',
									'documentos.nDoc as nDoc',
									'documentos.monto as monto',
									'documentos.ordenCompra as ordenCompra',
									'documentos.archivo as archivo',
									'documentos.nomina as nomina',
									'referentes.name as referente',
									'movimientos.estado as estado',
									'movimientos.observacion as observacion')							
							->selectRaw('count(documento_validadors.id) as cuenta_validator')
							->selectRaw('DATE_FORMAT(documentos.fechaRecepcion, "%d-%m-%Y") as fechaRecepcion')
							->where([
									['documentos.establecimiento_id','=',$estab_user],
									['movimientos.active',1],
									['referente_user.user_id',Auth::user()->id],
									['rut', 'LIKE', '%'.Session::get('searchRut').'%'],
									['tipo_docs.id', 'LIKE', '%'.Session::get('searchTipo').'%'],
									['nDoc', 'LIKE', '%'.Session::get('searchNdoc').'%']
									])
							->whereIn('movimientos.estado',['VB','DR','DO'])
							->groupBy('documentos.id')
							->groupBy('rut')
							->groupBy('proveedors.name')
							->groupBy('tipo_docs.name')
							->groupBy('documentos.id')
							->groupBy('documentos.nDoc')
							->groupBy('documentos.monto')
							->groupBy('documentos.ordenCompra')
							->groupBy('documentos.archivo')
							->groupBy('documentos.nomina')
							->groupBy('referentes.name')
							->groupBy('movimientos.estado')
							->groupBy('movimientos.observacion')
							->groupBy('fechaRecepcion');
			
			//agrega filtro por filtro de validadores de adjuntos
			if( Session::get('searchAdjunto') === '1') { //contiene adjunto
				$documentos = $documentos->havingRaw('count(documento_validadors.id) > 0');
			}
			elseif( Session::get('searchAdjunto') === '0') { //no contiene adjunto
				$documentos = $documentos->havingRaw('count(documento_validadors.id) = 0');
			}

			//paginacion y orden			
			$documentos = $documentos->orderBy('documentos.id','desc')->paginate(10)							
							->appends('searchRut',Session::get('searchRut'))
							->appends('searchTipo',Session::get('searchTipo'))
							->appends('searchNdoc',Session::get('searchNdoc'))
							->appends('searchAdjunto',Session::get('searchAdjunto'));
							
							
							
			
			$tipos = TipoDoc::where([['active',1],['flujo',1]])->orderBy('name')->get();
			
			return view('factura.referenteTecnico.index',compact('documentos','tipos'));
		}
		else {
			return view('auth/login');
		}
	}	
	
	/**
     * Funcion que Devuelve los documentos a convenios en caso de no pertinencia de Referente Técnico a Convenio.
	 * Vista: factura.referenteTecnico.devolver
	 * Rol: referenteTecnico
     *
     * @param  \Illuminate\Http\Request  $request
	 * @param $int id Identificador de Documento
	 * @return \Illuminate\Http\Response
     */
    public function devolverConvenio(Request $request,$id)
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			$documento = Documento::find($id);
			
			if ( $documento->establecimiento_id == $estab_user ) {	
				
				$proveedor = Proveedor::find($documento->proveedor_id);
				
				$tipo = TipoDoc::find($documento->tipoDoc_id);
				
				return view('factura.referenteTecnico.devolver',compact('documento','proveedor','tipo'));
			}
			else {
				return view('home');
			}
		}
		else {
			return view('auth/login');
		}
	}

	/**
     * Funcion Accion de Devolver documentos de Referente Técnico a Convenios
	 * Rol: referenteTecnico
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function movDevolver(Request $request) 
	{
		if (Auth::check()) {
			
			$mov = Movimiento::where([['documento_id',$request->documento_id],['active',1]])->first();
			
			//cambia estado de movimiento de activo a no activo
			$id = $mov->id; 
			$movimiento = Movimiento::find($id);
			$movimiento->active = 0;
			
			$movimiento->save();
			
			//guarda nuevo movimiento
			$movimientoNew = new Movimiento;
			
			$movimientoNew->documento_id = $request->documento_id;
			$movimientoNew->estado = 'DV';
			$movimientoNew->observacion = str_replace(PHP_EOL,"<br>",$request->observacion);
			$movimientoNew->user_id = Auth::user()->id;
				
			$movimientoNew->save();
			
			//almacena datos en ABEX
			try {
				$result = DB::connection('oracle')->update("UPDATE DOCUMENTOS SET ESTADO = 'DV', ESTADO_NOMBRE = 'Devuelto Referente Tecnico a Convenios', FECHA_MODIFICADO = '".$movimientoNew->created_at."' WHERE ID = ".$movimientoNew->documento_id);	
			}
			catch(Exception $e){}
			//fin datos en ABEX
			
			return redirect('referenteTecnico')->with('message','devolver');
			
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Funcion Adjuntar Validadores
	 * Vista: factura.referenteTecnico.adjuntar
	 * Rol: referenteTecnico
     *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int $id Identificador de Documento
	 * @return \Illuminate\Http\Response
	 */	 
	public function adjuntarValidadores(Request $request, $id) 
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			$documento = Documento::find($id);
			
			if ( $documento->establecimiento_id == $estab_user ) {	
			
				$proveedor = Proveedor::find($documento->proveedor_id);
				
				$tipo = TipoDoc::find($documento->tipoDoc_id);
				
				$validadores = DB::table('validadors')
							   ->join('convenio_validador', 'validadors.id','=','convenio_validador.validador_id')
							   ->join('documentos', 'convenio_validador.convenio_id','=','documentos.convenio_id')
							   ->leftjoin('documento_validadors', 
							               [['validadors.id','documento_validadors.validador_id'],
										    ['documento_validadors.documento_id','=',DB::raw($id)],
										    ['documento_validadors.active','=',DB::raw(1)]])
							   ->where('documentos.id',$id)
							   ->select('validadors.name as name',
							            'validadors.id as validador_id',
										'documento_validadors.archivo as archivo')
							   ->orderBy('validadors.name')->get();
				
				return view('factura.referenteTecnico.adjuntar',compact('documento','proveedor','tipo','validadores'));
			}
		}
		else {
			return view('auth/login');
		}	
	}
	
	/**
     * Funcion Adjuntar Validadores y Envia Documentos a Jefe Referente Técnico
	 * Rol: referenteTecnico
     *
	 * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
	 */	 
	public function movValidadores(Request $request)
	{
		if (Auth::check()) {
			
			$documento = Documento::find($request->documento_id);
			
			if($request->adjuntar == 2){ //Si adjunta documentos
				
				$validadores = DB::table('convenio_validador')
								   ->join('documentos', 'convenio_validador.convenio_id','=','documentos.convenio_id')
								   ->where('documentos.id',$documento->id)
								   ->select('convenio_validador.validador_id as id')->get();
				
				//valida si documento es tipo PDF 
				foreach ($validadores as $validador)
				{
						$adjunto = DocumentoValidador::where([['documento_id',$request->documento_id],['validador_id',$validador->id],['active','1']])->count();
						
						if ( $adjunto == 0 ) {
							$validator = validator::make($request->all(), [
								'f'.$validador->id => "nullable|mimes:pdf|max:10024",
							]);
							
							if ($validator->fails()) {
								return redirect('referenteTecnico/'.$request->documento_id.'/adjuntar')
										->withErrors($validator)
										->withInput();
							}
						}
						elseif ( $adjunto > 0 && $request->hasFile('f'.$validador->id) ) { 
							$validator = validator::make($request->all(), [
								'f'.$validador->id => "nullable|mimes:pdf|max:10024",
							]);
							
							if ($validator->fails()) {
								return redirect('referenteTecnico/'.$request->documento_id.'/adjuntar')
										->withErrors($validator)
										->withInput();
							}
						}
				}
				
				
				//adjunta doucumentos
				foreach ($validadores as $validador) 
				{
					//adjunta archivo
					$archivoName = null;
					$dv_old      = null;
					
					if ($request->hasFile('f'.$validador->id)) {
						
						$archivo = $request->file('f'.$validador->id);
						$archivoName = 'v'.time().$archivo->getClientOriginalName();
						
						//sube archivo
						$request->file('f'.$validador->id)->storeAs('public',$archivoName);
						
						//desactiva documento si existe anterior
						$dv_old = DocumentoValidador::where([['documento_id',$request->documento_id],['validador_id',$validador->id],['active','1']])->first();
						
						if ($dv_old != null ) {
							$aux = DocumentoValidador::find($dv_old->id);
							$aux->active = 0;
							$aux->save();
						}
						
						//guarda nombre de archivo
						$dv = new DocumentoValidador;
						
						$dv->documento_id = $request->documento_id;
						$dv->validador_id = $validador->id;
						$dv->archivo      = Storage::url($archivoName);
						$dv->active       = 1;
						
						$dv->save();
						
					}
					
				}
				
				return redirect('referenteTecnico/'.$request->documento_id.'/adjuntar');
			}
			else { //si envia documentos
				//guarda glosa memo automatico
				$documento->memoGlosa = $request->glosaMemo;
				$documento->save();
				
				//Genera el Movimiento
				$mov = Movimiento::where([['documento_id',$request->documento_id],['active',1]])->first();
				
				//cambia estado de movimiento de activo a no activo
				$id = $mov->id; 
				$movimiento = Movimiento::find($id);
				$movimiento->active = 0;
				
				$movimiento->save();
				
				//guarda nuevo movimiento
				$movimientoNew = new Movimiento;
				
				$movimientoNew->documento_id = $request->documento_id;
				$movimientoNew->estado = 'RT';
				$movimientoNew->observacion = str_replace(PHP_EOL,"<br>",$request->observacion);
				$movimientoNew->user_id = Auth::user()->id;
				
				$movimientoNew->save();
				
				//almacena datos en ABEX
				try {
					$result = DB::connection('oracle')->update("UPDATE DOCUMENTOS SET ESTADO = 'RT', ESTADO_NOMBRE = 'Jefe Referente Tecnico - Por Validar', FECHA_MODIFICADO = '".$movimientoNew->created_at."' WHERE ID = ".$movimientoNew->documento_id);	
				}
				catch(Exception $e){}
				//fin datos en ABEX
									
				return redirect('referenteTecnico')->with('message','envio');
			}
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Funcion Reporte Documentos Validados.
	 * Vista: factura.referenteTecnico.validados
	 * Rol: referenteTecnico
     *
     * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
     */
	public function reporteValidados(Request $request) 
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			$documentos =  DB::table('documentos')
							->join('movimientos', 'documentos.id','=','movimientos.documento_id')
							->join('proveedors','documentos.proveedor_id','=','proveedors.id')
							->join('tipo_docs','documentos.tipoDoc_id','=','tipo_docs.id')
							->join('convenios','documentos.convenio_id','=','convenios.id')
							->join('referentes','convenios.referente_id','=','referentes.id')
							->join('referente_user','referentes.id','=','referente_user.referente_id')
							->select('proveedors.rut as rut',
									 'proveedors.name as nameProveedor',
									 'tipo_docs.name as tipoDoc',
									 'documentos.id as id',
									 'documentos.nDoc as nDoc',
									 'documentos.monto as monto',
									 'documentos.ordenCompra as ordenCompra',
									 'documentos.archivo as archivo',
									 'documentos.nomina as nomina',
									 'referentes.name as referente')
							->selectRaw('DATE_FORMAT(documentos.fechaRecepcion, "%d-%m-%Y") as fechaRecepcion')
							->where([
									['documentos.establecimiento_id','=',$estab_user],
									['movimientos.active',1],
									['referente_user.user_id',Auth::user()->id],
									['rut', 'LIKE', '%'.$request->get('searchRut').'%'],
									['tipo_docs.id', 'LIKE', '%'.$request->get('searchTipo').'%'],
									['nDoc', 'LIKE', '%'.$request->get('searchNdoc').'%'],
									])
							->whereIn('movimientos.estado',['RC','DE','TE','EN','FN'])
							->orderBy('documentos.id','desc')->paginate(10)
							->appends('searchRut',$request->get('searchRut'))
							->appends('searchTipo',$request->get('searchTipo'))
							->appends('searchNdoc',$request->get('searchNdoc'));
			
			$tipos = TipoDoc::where([['active',1],['flujo',1]])->orderBy('name')->get();
			
			return view('factura.referenteTecnico.validados',compact('documentos','tipos'));
		}
		else {
			return view('auth/login');
		}
	}

	
	/**
	 * Funcion que Carga los validadores de Forma Masiva
	 * Vista: factura.referenteTecnico.resultado_cm_validadores
	 * Rol: referenteTecnico
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function uploadcargavalidadores(Request $request)
    {	
        if (Auth::check()) 
		{	try
			{			
				$user = User::find(Auth::user()->id);
				$estab_user = $user->establecimientos()->first()->id;	
				// Verifica formato pdf y el tamaño menor a 10MB
				$ar = validator::make($request->all(),[
					"archivo" =>"array|nullable|required",
					"archivo.*" => "nullable|required",
					"tipo"=>"required|nullable",
				]);
			// Se guardan los errores y se generan un contador
				$errores=[]; // nombre de los archivos con error
				$cont   = 0;
				$error_count = array(
				1 => 0, 
				2 => 0,
				3 => 0,
				4 => 0,
				5 => 0,
				6 => 0
				);
				$mensaje = array(
				1 => "Documento no existe", 
				2 => "Documento duplicado",
				3 => "El documento debe estar en estado: por validar en referente técnico",
				4 => "El formato del documento no es PDF",
				5 => "Supera el tamaño maximo de 10MB",
				6 => "El validador ingresado no es requerido",
				);
						
				// Recibe los archivos pdf enviados
				if ($ar->fails()) 
				{
					return redirect('cargaValidadores/index')
						->withErrors($ar)
						->withInput();
						
				}
				// Recibe los archivos
				$archivos=request()->file('archivo');
				if(count($archivos)>100)
				{
					$errors=array('maximo'=>'true'); 
					return redirect('cargaValidadores/index')
						->withErrors($errors)
						->withInput();
				}

				// Recorre los archivos 
				foreach ($archivos as $as) 
				{
					// Consigue los nombres de los archivos
					$nombre=$as->getClientOriginalName();
					$arra = $nombre; // almacena el nombre del documento
					// Obtiene el tipo del archivo
					$tipoFile = $as->getMimeType();
					// Valida que el documento sea formato pdf
					if( $tipoFile != "application/pdf")
					{
						// Envia el error
						$texto=$arra."::".$mensaje[4]."::4";
						array_push($errores, $texto);
						$error_count[4]=$error_count[4]+1;
						continue;
					}
					//Genera un array para la validación
					$array = array("as" => $as);
					// Valida el tamaño del archivo 
					$validator = validator::make($array,[
						"as" => "max:10240"
					]);

					if ($validator->fails()) 
					{
						// Envia el error					
						$texto=$arra."::".$mensaje[5]."::5";
						array_push($errores, $texto);
						$error_count[5]=$error_count[5]+1;
						continue;
					}
					$arra = str_replace(".pdf","",$arra); //Elimina la extension del documento
					if($this->IsNumeric($arra)){ /* $arra es el nombre del documento */ 
						// Consulta para preguntar si el archivo existe 
						$documentos =DB::table('documentos')
							->select('id')
							->where('nDoc','=',$arra)
							->get();
					}
					else{
						$documentos = null;
					}				
					// Verifica si el archivo existe        
					if (count($documentos)<>0)
					{	
						// Estado del archivo
						$documentos =DB::table('documentos')
						->join('movimientos', 'documentos.id','=','movimientos.documento_id')
						->select('documentos.id as id','documentos.convenio_id as convenio_id')
						->where('documentos.nDoc','=',$arra)
						->where('movimientos.active','=','1')
						->where('movimientos.estado','=','VB')
						->get();
						// Verifica que exista solo una vez
						if(count($documentos)==1)
						{  
							// Recorre el documentos para sacar el id
							foreach ($documentos as $value) 
							{	
								// Consigue el id 
								$documento_id=$value->id;
								$convenio_id = $value->convenio_id;
								// Recibe el validador del archivo
								$validador_id=$request->tipo;
								// Consulta si el referente tiene el documento asignado
								$asignado = DB::table('convenios')
								->join('referente_user', 'referente_user.referente_id','=','convenios.referente_id')
								->WHERE('referente_user.user_id',$user->id)
								->WHERE('convenios.id',$convenio_id)
								->exists();
								if (!$asignado){
									// Requiere estado VB                       
									$texto3=$arra.".pdf::".$mensaje[3]."::3";
									array_push($errores, $texto3);
									$error_count[3]=$error_count[3]+1;
									continue;
								}
								// Consulta para conseguir el estado y que este activo
								$vali =DB::table('convenio_validador')
									->select('*')
									->where('convenio_id','=',$convenio_id)
									->where('validador_id','=',$validador_id)
									->get();
								// Verifica si el validador es requerido
								if(count($vali)<>1)
								{ 
									$texto6=$arra.".pdf::".$mensaje[6]."::6";
									array_push($errores, $texto6);
									$error_count[6]=$error_count[6]+1;
									continue;
								}								
								// Recorre la consulta para conseguir el estado    
								foreach ($vali as  $v) 
								{								
									// Verifica que sea igual a VB
									$dv_old = null;								
									// Da nombre al archivo para ser guardado en la base de datos  
									$archivoName = 'v'.time().$nombre;
									// Guarda archivo
									$as->storeAs('/public/',$archivoName);
									// Desactiva documento si existe anterior
									$dv_old = DocumentoValidador::where([['documento_id',$documento_id],['validador_id',$validador_id],['active','1']])->first();
									if ($dv_old != null ) 
									{ 
										$aux = DocumentoValidador::find($dv_old->id);
										$aux->active = 0; // Establece el estado anterior en 0
										$aux->save();
									}
									// Guarda en la base de datos
									$dv = new DocumentoValidador;
									
									$dv->documento_id = $documento_id;
									$dv->validador_id = $validador_id;
									$dv->archivo = Storage::url($archivoName);
									$dv->active = 1;
									$dv->save();
									
									// Se guarda en un contador los archivos subidos con exito
									$cont=$cont + 1;								
								// Fin foreach 
								} 							
							// Fin foreach      
							}
						// Fin if
						}
						else
						{
							if(count($documentos)>1){
								// Duplicado n_doc en el mismo estado
								$texto2=$arra.".pdf::".$mensaje[2]."::2";
								array_push($errores, $texto2);
								$error_count[2]=$error_count[2]+1;
								continue;
							}
							else
							{  
								// Requiere estado VB                       
								$texto3=$arra.".pdf::".$mensaje[3]."::3";
								array_push($errores, $texto3);
								$error_count[3]=$error_count[3]+1;
								continue;
							}
						}						
					// Fin if    
					}
					else
					{                     
						$texto1=$arra.".pdf::".$mensaje[1]."::1";
						array_push($errores, $texto1);
						$error_count[1]=$error_count[1]+1;
						continue;
					}
				// Fin foreach 
				}
				// Envia errores y cargas concretadas de los documentos enviados.
				Session::put('errores', $errores);                
				return view('factura.referenteTecnico.resultado_cm_validadores',compact('error_count','mensaje','cont','errores'));
									
			// Fin Try  
			}
			catch(Exception $e){
				$errors=array('maximo'=>'true'); 
					return redirect('cargaValidadores/index')
					->withErrors($errors)
					->withInput();
			}

		// Fin if	
		}	
        else 
        {
		  return view('auth/login');
		}		
	}
	 
	/**
	 * Funcion que llama a la pantalla de carga masiva de validadores
	 * Vista: factura.referenteTecnico.ingreso_cm_validadores
	 * Rol: referenteTecnico
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function vistaSelectValidadores(Request $request)
	{        
	   if (Auth::check()) 
	   {
		   $consulta=Validador::select('id','name')->WHERE('active',1)->orderBy('name')->get();	
		   return view('factura.referenteTecnico.ingreso_cm_validadores',compact('consulta'));
	   }
	   else 
	   {
		   return view('auth/login');
	   }
	}

	/**
	 * Funcion que genera Excel de la respuesta Carga Masiva de Validadores
	 * Rol: referenteTecnico
	 *
     * @param  \Illuminate\Http\Request  $request	 
	 * @return lista de documentos incluidos en SIGDOC (Todos los Establecimientos)
	 */
	public function excelRespuestaCargavalidadores(Request $request) 
	{
		if (Auth::check()) 
		{
			if (Session::has('errores'))
			{
				ini_set("memory_limit", -1);
				ini_set('max_execution_time', 300);
				$errores= Session::get('errores');//Recupera un objeto 
				//Session::forget('respuestas'); //Elimina la variable en session	
				//crea array con información de consulta
				$documentosArray[] = ['nombre_documento','error'];
					
				foreach($errores as $errore)
				{						
					$errorest = explode("::", $errore);
					$nombre = $errorest[0]; //nombre de los pdf que no se adjuntaron
					$error=$errorest[1]; //error de adjuntar documento
					$documentosArray[] = [ $nombre,$error];
				}

				//Genera archivo Excel
				Excel::create('Resultado_CM_'.date("d-m-Y_H:i"), function($excel) use($documentosArray) {
					$excel->sheet('Documentos', function($sheet) use($documentosArray) {
						$sheet->fromArray($documentosArray,null,'A1',true,false);
			 		});
				})->export('xls');

			}
			else
			{
				return view('auth/login');
			}
        }
		else 
		{
			return view('auth/login');
		}
			        
	}
	

	
	/*******************************************************************************************/
	/*                                JEFE REFERENTE TECNICO                                   */
	/*******************************************************************************************/
	/**
     * Funcion que Lista los documentos que se encuentran en el jefe de referente tecnico.
	 * Vista: factura.jefeRt.index
	 * Rol: jefeRt
     *
     * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
     */
    public function jefeRt(Request $request)
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;	

			//guarda en sesion los filtros de documentos
			if(Session::get('message') == null) {
				Session::forget('searchRut'); //Elimina la variable en session
				Session::forget('searchTipo'); //Elimina la variable en session
				Session::forget('searchNdoc'); //Elimina la variable en session
				
				Session::put('searchRut', $request->get('searchRut'));
				Session::put('searchTipo', $request->get('searchTipo'));
				Session::put('searchNdoc', $request->get('searchNdoc'));
			}	
			
			$documentos =  DB::table('documentos')
							->join('movimientos', 'documentos.id','=','movimientos.documento_id')
							->join('proveedors','documentos.proveedor_id','=','proveedors.id')
							->join('tipo_docs','documentos.tipoDoc_id','=','tipo_docs.id')
							->join('convenios','documentos.convenio_id','=','convenios.id')
							->join('referentes','convenios.referente_id','=','referentes.id')
							->join('referente_user','referentes.id','=','referente_user.referente_id')
							->select('proveedors.rut as rut',
									 'proveedors.name as nameProveedor',
									 'tipo_docs.name as tipoDoc',
									 'documentos.id as id',
									 'documentos.nDoc as nDoc',
									 'documentos.monto as monto',
									 'documentos.ordenCompra as ordenCompra',
									 'documentos.archivo as archivo',
									 'documentos.nomina as nomina',
									 'referentes.name as referente',
									 'movimientos.estado as estado',
									 'movimientos.observacion as observacion')
							->selectRaw('DATE_FORMAT(documentos.fechaRecepcion, "%d-%m-%Y") as fechaRecepcion')
							->where([
									['documentos.establecimiento_id','=',$estab_user],
									['movimientos.active',1],
									['referente_user.user_id',Auth::user()->id],
									['rut', 'LIKE', '%'.Session::get('searchRut').'%'],
									['tipo_docs.id', 'LIKE', '%'.Session::get('searchTipo').'%'],
									['nDoc', 'LIKE', '%'.Session::get('searchNdoc').'%'],
									])
							->whereIn('movimientos.estado',['RT'])		
							->orderBy('documentos.id','desc')->paginate(10)
							->appends('searchRut',Session::get('searchRut'))
							->appends('searchTipo',Session::get('searchTipo'))
							->appends('searchNdoc',Session::get('searchNdoc'));
			
			$tipos = TipoDoc::where([['active',1],['flujo',1]])->orderBy('name')->get();
			
			return view('factura.jefeRt.index',compact('documentos','tipos'));
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Funcion que Devuelve los documentos a referente tecnico en caso de observacion.
	 * Vista: factura.jefeRt.devolver
	 * Rol: jefeRt
     *
     * @param  \Illuminate\Http\Request  $request
	 * @param int $id Identificador de Documento
	 * @return \Illuminate\Http\Response
     */
    public function devolverReferente(Request $request,$id)
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			$documento = Documento::find($id);
			
			if ( $documento->establecimiento_id == $estab_user ) {	
				
				$proveedor = Proveedor::find($documento->proveedor_id);
				
				$tipo = TipoDoc::find($documento->tipoDoc_id);
				
				return view('factura.jefeRt.devolver',compact('documento','proveedor','tipo'));
			}
			else {
				return view('home');
			}
		}
		else {
			return view('auth/login');
		}
	}

	/**
     * Funcion de Accion de Devolver documentos de Jefe de Referente Tecnico a Referente Tecnico
	 * Rol: jefeRt
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function movDevolverRt(Request $request) 
	{
		if (Auth::check()) {
			
			$mov = Movimiento::where([['documento_id',$request->documento_id],['active',1]])->first();
			
			//cambia estado de movimiento de activo a no activo
			$id = $mov->id; 
			$movimiento = Movimiento::find($id);
			$movimiento->active = 0;
			
			$movimiento->save();
			
			//guarda nuevo movimiento
			$movimientoNew = new Movimiento;
			
			$movimientoNew->documento_id = $request->documento_id;
			$movimientoNew->estado = 'DR';
			$movimientoNew->observacion = str_replace(PHP_EOL,"<br>",$request->observacion);
			$movimientoNew->user_id = Auth::user()->id;
				
			$movimientoNew->save();
			
			//almacena datos en ABEX
			try {
				$result = DB::connection('oracle')->update("UPDATE DOCUMENTOS SET ESTADO = 'DR', ESTADO_NOMBRE = 'Devuelto Jefe R.T. a Referente Tecnico', FECHA_MODIFICADO = '".$movimientoNew->created_at."' WHERE ID = ".$movimientoNew->documento_id);	
			}
			catch(Exception $e){}
			//fin datos en ABEX
			
			return redirect('jefeReferenteTecnico')->with('message','devolver');
			
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Funcion que Envia documentos a Oficina de Convenios
	 * Rol: jefeRt
     *
     * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
     */
	public function enviarConvenio(Request $request)
	{
		if (Auth::check()) {
			$checks = $request->check_list;
			
			if(!empty($checks)) {
				foreach($checks as $check) {
					$documento = Documento::find($check);
					
					$mov = Movimiento::where([['documento_id',$check],['active',1]])->first();
					
					//cambia estado de movimiento de activo a no activo
					$id = $mov->id; 
					$movimiento = Movimiento::find($id);
					$movimiento->active = 0;
					
					$movimiento->save();
					
					//guarda nuevo movimiento
					$movimientoNew = new Movimiento;
					
					$movimientoNew->documento_id = $check;
					$movimientoNew->estado = 'RC';
					$movimientoNew->user_id = Auth::user()->id;
						
					$movimientoNew->save();
					
					//almacena datos en ABEX
					try {
						$result = DB::connection('oracle')->update("UPDATE DOCUMENTOS SET ESTADO = 'RC', ESTADO_NOMBRE = 'Documento Validado por Referente Tecnico', FECHA_MODIFICADO = '".$movimientoNew->created_at."' WHERE ID = ".$movimientoNew->documento_id);	
					}
					catch(Exception $e){}
					//fin datos en ABEX
				}
				
			}	
			return redirect('jefeReferenteTecnico')->with('message','envio');
		}
		else {
			return view('auth/login');
		}
	}
    /*******************************************************************************************/
	/*                                      CONTABILIDAD                                       */
	/*******************************************************************************************/
	/**
     * Funcion que Lista los documentos que se encuentran en contabilidad.
	 * Vista: factura.contabilidad.index
	 * Rol: contabilidad
     *
     * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
     */
    public function contabilidad(Request $request)
	{
		if (Auth::check()) { 
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			$documentos =  DB::table('documentos')
							->join('movimientos', 'documentos.id','=','movimientos.documento_id')
							->join('proveedors','documentos.proveedor_id','=','proveedors.id')
							->join('tipo_docs','documentos.tipoDoc_id','=','tipo_docs.id')
							->leftjoin('convenios','documentos.convenio_id','=','convenios.id')
							->leftjoin('referentes','convenios.referente_id','=','referentes.id')
							->select('proveedors.rut as rut',
									 'proveedors.name as nameProveedor',
									 'tipo_docs.name as tipoDoc',
									 'documentos.id as id',
									 'documentos.nDoc as nDoc',
									 'documentos.monto as monto',
									 'documentos.ordenCompra as ordenCompra',
									 'documentos.archivo as archivo',
									 'documentos.nomina as nomina',
									 'documentos.devengo_clasificador_id as devengo_item',
									 'referentes.name as referente',
									 'movimientos.estado as estado',
									 'movimientos.observacion as observacion')
							->selectRaw('DATE_FORMAT(documentos.fechaRecepcion, "%d-%m-%Y") as fechaRecepcion')
							->where([
									['documentos.establecimiento_id','=',$estab_user],
									['movimientos.active',1],
									['rut', 'LIKE', '%'.$request->get('searchRut').'%'],
									['tipo_docs.id', 'LIKE', '%'.$request->get('searchTipo').'%'],
									['nDoc','LIKE',$request->get('searchNdoc')],
									['estado', 'LIKE', '%'.$request->get('searchEstado').'%'],
									])
							->whereIn('movimientos.estado',['VB','DV','DO','DE','RT','DR','RC','DC'])		
							->orderBy('documentos.id','desc')->paginate(10)
							->appends('searchRut',$request->get('searchRut'))
							->appends('searchTipo',$request->get('searchTipo'))
							->appends('searchNdoc',$request->get('searchNdoc'))
							->appends('searchEstado',$request->get('searchEstado'));
			
			$tipos = TipoDoc::where([['active',1],['flujo',1]])->orderBy('name')->get();
			
			//parametros de filtro
			$searchRut    = $request->get('searchRut');
			$searchTipo   = $request->get('searchTipo');
			$searchNdoc   = $request->get('searchNdoc');
			$searchEstado = $request->get('searchEstado');
			$page         = $request->get('page');
			
			return view('factura.contabilidad.index',compact('documentos','tipos','searchRut','searchTipo','searchNdoc','searchEstado','page'));
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Funcion que Devuelve los documentos de contabilidad a convenios en caso de observacion.
	 * Vista: factura.contabilidad.devolver
	 * Rol: contabilidad
     *
     * @param  \Illuminate\Http\Request  $request
	 * @param int $id Identificador de Documento
	 * @return \Illuminate\Http\Response
     */
    public function devolverContabilidad(Request $request,$id)
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			$documento = Documento::find($id);
			
			if ( $documento->establecimiento_id == $estab_user ) {	
				$proveedor = Proveedor::find($documento->proveedor_id);
				
				$tipo = TipoDoc::find($documento->tipoDoc_id);
				
				return view('factura.contabilidad.devolver',compact('documento','proveedor','tipo'));
			}
			else {
				return view('home');
			}
		}
		else {
			return view('auth/login');
		}
	}

	/**
     * Funcion accion de Devolver documentos de contabilidad a convenios
	 * Rol: contabilidad
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function movDevolverCo(Request $request) 
	{
		if (Auth::check()) {
			
			$mov = Movimiento::where([['documento_id',$request->documento_id],['active',1]])->first();
			
			//cambia estado de movimiento de activo a no activo
			$id = $mov->id; 
			$movimiento = Movimiento::find($id);
			$movimiento->active = 0;
			
			$movimiento->save();
			
			//guarda nuevo movimiento
			$movimientoNew = new Movimiento;
			
			$movimientoNew->documento_id = $request->documento_id;
			$movimientoNew->estado = 'DC';
			$movimientoNew->observacion = str_replace(PHP_EOL,"<br>",$request->observacion);
			$movimientoNew->user_id = Auth::user()->id;
				
			$movimientoNew->save();
			
			//almacena datos en ABEX
			try {
				$result = DB::connection('oracle')->update("UPDATE DOCUMENTOS SET ESTADO = 'DC', ESTADO_NOMBRE = 'Devuelto Contabilidad a Convenios', FECHA_MODIFICADO = '".$movimientoNew->created_at."' WHERE ID = ".$movimientoNew->documento_id);	
			}
			catch(Exception $e){}
			//fin datos en ABEX
			
			return redirect('contabilidad')->with('message','devolver');
			
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Funcion que Devenga documentos
	 * Vista: factura.contabilidad.devengar
	 * Rol: contabilidad
     *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id Identificador de Documentos
	 * @return \Illuminate\Http\Response
     */
	public function devengar(Request $request, $id) 
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			$documento = Documento::find($id);
			
			//parametros de filtro
			$searchRut    = $request->get('searchRut');
			$searchTipo   = $request->get('searchTipo');
			$searchNdoc   = $request->get('searchNdoc');
			$searchEstado = $request->get('searchEstado');
			$page         = $request->get('page');         
			
			if ( $documento->establecimiento_id == $estab_user && Auth::user()->isRole('Contabilidad') ) {	
				
				$proveedor      = Proveedor::find($documento->proveedor_id);
				$tipo           = TipoDoc::find($documento->tipoDoc_id);
				$clasificadores = Clasificador::where('active',1)->orderBy('codigo')->get();
				
				return view('factura.contabilidad.devengar',compact('documento','proveedor','tipo','flujo','clasificadores','searchRut','searchTipo','searchNdoc','searchEstado','page'));
			}
			else {
				return view('home');
			}			
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Funcion que Guarda datos de devengo de documentos
	 * Rol: contabilidad
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function movDevengar(Request $request) 
	{
		
		if (Auth::check()) {
			$validator = validator::make($request->all(), [
				'fecha'       => 'required',
			]);	
			if ($validator->fails()) {
				return redirect('contabilidad/'.$request->documento_id.'/devengar/')
							->withErrors($validator)
							->withInput();
			}
			else {
				$documento = Documento::find($request->documento_id);
				
				$fecha = DateTime::createFromFormat('d-m-Y', $request->fecha);
				
				$documento->devengo_clasificador_id = $request->clasificador;
				$documento->devengo_fecha           = $fecha;
				$documento->devengo_observacion     = $request->observacion;
				$documento->save();
				
				//parametros de filtro
				$searchRut    = $request->get('searchRut');
				$searchTipo   = $request->get('searchTipo');
				$searchNdoc   = $request->get('searchNdoc');
				$searchEstado = $request->get('searchEstado');	
				$page         = $request->get('page');         	
				
				return redirect('contabilidad?searchNdoc='.$searchNdoc.'&searchRut='.$searchRut.'&searchTipo='.$searchTipo.'&searchEstado='.$searchEstado.'&page='.$page)->with('message','devengo');
			}
		}
		else {
			return view('auth/login');
		}	
	}
	
	/**
     * Funcion que Envia los documentos a tesoreria
	 * Rol: contabilidad
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function enviarTesoreria(Request $request) 
	{
		if (Auth::check()) {
			$aux = 0;
			
			$checks = $request->check_list;
			
			if(!empty($checks)) {
				foreach($checks as $check) {
					$documento = Documento::find($check);
					
					if ($documento->devengo_clasificador_id != null) { //envío documentos solo devengados
					
							$mov = Movimiento::where([['documento_id',$check],['active',1]])->first();
							
							if ( $mov->estado == 'DE' ) {
								//cambia estado de movimiento de activo a no activo
								$id = $mov->id; 
								$movimiento = Movimiento::find($id);
								$movimiento->active = 0;
								
								$movimiento->save();
								
								//guarda nuevo movimiento
								$movimientoNew = new Movimiento;
								
								$movimientoNew->documento_id = $check;
								$movimientoNew->estado = 'TE';
								$movimientoNew->user_id = Auth::user()->id;
									
								$movimientoNew->save();
								
								//almacena datos en ABEX
								try {
									$result = DB::connection('oracle')->update("UPDATE DOCUMENTOS SET ESTADO = 'TE', ESTADO_NOMBRE = 'En Tesoreria para Pago', FECHA_MODIFICADO = '".$movimientoNew->created_at."' WHERE ID = ".$movimientoNew->documento_id);	
								}
								catch(Exception $e){}
								//fin datos en ABEX
							}
							else {
								$aux = $aux + 1;
							}	
					}
					else {
						$aux = $aux + 1;
					}
				}
			}
			if ($aux == 0) {
				return redirect('contabilidad')->with('message','envio');
			}
			else {
				return redirect('contabilidad')->with('message','envioWarning');
			}
		}
		else {
			return view('auth/login');
		}
	}

	/**
     * Funcion que genera Reporte contabilidad de los devengados y no devengados por pagar
	 * Vista: factura.contabilidad.resultadoContabilidad
	 * Rol: contabilidad
     *
     * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
     */
	public function reporteContabilidad(Request $request)
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab   = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab      = $user->establecimientos()->first()->id;
			
			$documentos =  DB::table('documentos')
							->join('movimientos', 'documentos.id','=','movimientos.documento_id')
							->join('proveedors','documentos.proveedor_id','=','proveedors.id')
							->join('tipo_docs','documentos.tipoDoc_id','=','tipo_docs.id')
							->join('establecimientos','documentos.establecimiento_id','=','establecimientos.id')
							->leftjoin('tipo_pagos','documentos.pago_tipo_id','=','tipo_pagos.id')
							->leftjoin('convenios','documentos.convenio_id','=','convenios.id')
							->leftjoin('referentes','convenios.referente_id','=','referentes.id')
							->orderBy('documentos.id','desc')
							->select('proveedors.rut as rut',
									 'proveedors.name as nameProveedor',
									 'tipo_docs.name as tipoDoc',
									 'documentos.id as id',
									 'documentos.nDoc as nDoc',
									 'documentos.fechaRecepcion as fechaRecepcion',
									 'documentos.ordenCompra as ordenCompra',
									 'documentos.archivo as archivo',
									 'documentos.nomina as nomina',
									 'documentos.monto',
									 'documentos.pago_operacion as pago_operacion',
									 'documentos.pago_sigfe as pago_sigfe',
									 'documentos.devengo_clasificador_id as devengo_item',
									 'establecimientos.name as establecimiento',
									 'convenios.identificador as convenio',
									 'referentes.name as referente',
									 'tipo_pagos.name as tipoPago',
									 'movimientos.estado as estado',
									 'movimientos.observacion as observacion')
							->selectRaw('DATE_FORMAT(documentos.fechaRecepcion, "%d-%m-%Y") as fechaRecepcion')
							->selectRaw('DATE_FORMAT(documentos.pago_fechaPago, "%d-%m-%Y") as fechaPago')
							->where([['movimientos.active',1],['documentos.establecimiento_id',$estab]]);
							
			//$documentos = $documentos->whereNotNull('documentos.devengo_clasificador_id'); // Verifica si ha sido devengado
			$documentos = $documentos->where('estado','<>','RE'); // Diferente a Rechazado			
			$documentos = $documentos->where('estado','<>','FN'); // Diferente a Entregado
			$documentos = $documentos->where('estado','<>','EN'); // Diferente a En Tesorería para Entrega				
			
			$searchDevengo = $request->get('searchDevengo');
			if($searchDevengo=='0'){
				$documentos = $documentos->whereNull('documentos.devengo_clasificador_id'); // Verifica si NO ha sido devengado
			}
			if($searchDevengo=='1'){
				$documentos = $documentos->whereNotNull('documentos.devengo_clasificador_id'); // Verifica si ha sido devengado
			}

			$documentos	= $documentos->paginate(10);

			return view('factura.contabilidad.resultadoContabilidad',compact('documentos','searchDevengo'));
		}
		else {
			return view('auth/login');
		}		

	}
	/**
	 * Funcion que genera archivo Excel reporte según filtros definidos por el usuario en formato excel
     * Rol: contabilidad
	 * 
	 * @param  \Illuminate\Http\Request  $request
	 * @return list lista de contrarreferencias que coinciden con la busqueda
	 */
	public function excelContabilidad(Request $request) 
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab   = $request->session()->get('establecimiento');
			ini_set("memory_limit", -1);
			ini_set('max_execution_time', 300);

			$user       = User::find(Auth::user()->id);
			$estab      = $user->establecimientos()->first()->id;
			
			$documentos =  DB::table('documentos')
							->join('movimientos', 'documentos.id','=','movimientos.documento_id')
							->join('proveedors','documentos.proveedor_id','=','proveedors.id')
							->join('tipo_docs','documentos.tipoDoc_id','=','tipo_docs.id')
							->join('establecimientos','documentos.establecimiento_id','=','establecimientos.id')
							->leftjoin('tipo_pagos','documentos.pago_tipo_id','=','tipo_pagos.id')
							->leftjoin('convenios','documentos.convenio_id','=','convenios.id')
							->leftjoin('referentes','convenios.referente_id','=','referentes.id')
							->orderBy('documentos.id','desc')
							->select('proveedors.rut as rut',
									 'proveedors.name as nameProveedor',
									 'tipo_docs.name as tipoDoc',
									 'tipo_docs.resta as resta',
									 'documentos.id as id',
									 'documentos.nDoc as nDoc',
									 'documentos.fechaRecepcion as fechaRecepcion',
									 'documentos.ordenCompra as ordenCompra',
									 'documentos.archivo as archivo',
									 'documentos.nomina as nomina',
									 'documentos.monto',
									 'documentos.pago_operacion as pago_operacion',
									 'documentos.pago_sigfe as pago_sigfe',
									 'documentos.devengo_clasificador_id',
									 'documentos.devengo_observacion',
									 'documentos.devengo_fecha',
									 'establecimientos.name as establecimiento',
									 'convenios.identificador as convenio',
									 'referentes.name as referente',
									 'tipo_pagos.name as tipoPago',
									 'movimientos.estado as estado',
									 'movimientos.observacion as observacion')
							->selectRaw('DATE_FORMAT(documentos.fechaEmision, "%d-%m-%Y") as fechaEmision')
							->selectRaw('DATE_FORMAT(documentos.fechaRecepcion, "%d-%m-%Y") as fechaRecepcion')
							->selectRaw('DATE_FORMAT(documentos.pago_fechaPago, "%d-%m-%Y") as fechaPago')
							->where([['movimientos.active',1],['documentos.establecimiento_id',$estab]]);

			//$documentos = $documentos->whereNotNull('documentos.devengo_clasificador_id'); // Verifica si ha sido devengado
			$documentos = $documentos->where('estado','<>','RE'); // Diferente a Rechazado			
			$documentos = $documentos->where('estado','<>','FN'); // Diferente a Entregado
			$documentos = $documentos->where('estado','<>','EN'); // Diferente a En Tesorería para Entrega
			$searchDevengo = $request->get('devengo'); // trae desde el formulario de exportacion a excel la variable devengo
			if($searchDevengo=='0'){
				$documentos = $documentos->whereNull('documentos.devengo_clasificador_id'); // Verifica si NO ha sido devengado
			}
			if($searchDevengo=='1'){
				$documentos = $documentos->whereNotNull('documentos.devengo_clasificador_id'); // Verifica si ha sido devengado
			}

			$documentos	= $documentos->paginate(10000);
			//tipoDoc','movimientos','tipoPago','cuenta','item'
			//crea array con información de consulta
			$documentosArray[] = ['Nombre Proveedor','RUT Proveedor','Tipo de Documento','Nro de Documento','Monto','Fecha de Emisión','Fecha de Recepción','Referente Técnico','Establecimiento','Estado','Devengado','Item de Devengo', 'Observación de Devengo', 'Fecha de Devengo'];

			foreach ($documentos->chunk(10) as $chunks) {
				foreach ($chunks as $documento)	{
					$nom_proveedor = $documento->nameProveedor;
					$rut_proveedor = $documento->rut;
					$tipoDoc   = $documento->tipoDoc;
					$numDoc    = $documento->nDoc;
					$monto     = $documento->monto;
					if($documento->resta==1){
						$monto     = ($documento->monto * -1);
					}
					$fechaEmision  = $documento->fechaEmision;
					$fechaRecepcion  = $documento->fechaRecepcion;
					$referente       = $documento->referente;
					$establecimiento = $documento->establecimiento;
					switch ($documento->estado) {
						case 'OP': $estado =  "Ingresado Oficina de Partes"; break;
						case 'NP': $estado =  "Recepción Secretaría de Convenios"; break;	
						case 'CV': $estado =  "Asignar a Referente Técnico"; break;
						case 'VB': $estado =  "Referente Técnico - Por Validar"; break;
						case 'RT': $estado =  "Jefe Referente Técnico - Por Validar"; break;
						case 'RC': $estado =  "Documento Validado por Referente Técnico"; break;
						case 'DE': $estado =  "En Contabilidad"; break;
						case 'TE': $estado =  "En Tesorería para Pago"; break;
						case 'EN': $estado =  "En Tesorería para Entrega"; break;
						case 'DV': $estado =  "Devuelto Referente Técnico a Convenios"; break;
						case 'DR': $estado =  "Devuelto Jefe R.T. a Referente Técnico"; break;	 
						case 'DO': $estado =  "Devuelto Convenios a Referente Técnico"; break;
						case 'DC': $estado =  "Devuelto Contabilidad a Convenios"; break;
						case 'FN': $estado =  "Entregado"; break;
						case 'RE': $estado =  "Rechazado"; break;													
					}

					try {

						$devengado='SI';
						$item = Clasificador::find($documento->devengo_clasificador_id);
						$item_devengo = $item->codigo;
						$devengo_observacion = $documento->devengo_observacion;
						$devengo_fecha = $documento->devengo_fecha;
						
					} catch (Exception $e) {
						$devengado='NO';
						$item_devengo = '';
						$devengo_observacion = '';
						$devengo_fecha = '';
					}								

					$documentosArray[] = [ $nom_proveedor,
										   $rut_proveedor,
	                                 	   $tipoDoc,
										   $numDoc,
										   $monto,
										   $fechaEmision,
										   $fechaRecepcion,
										   $referente,
										   $establecimiento,
										   $estado,
										   $devengado,
										   $item_devengo,
										   $devengo_observacion,
										   $devengo_fecha];
				}
			}	
			
			//Genera archivo Excel
			Excel::create('Archivo', function($excel) use($documentosArray) {
 				$excel->sheet('Documentos', function($sheet) use($documentosArray) {
					$sheet->fromArray($documentosArray,null,'A1',true,false);
	 			});
			})->export('xls');
		}
		else {
			return view('auth/login');
		}	
	}
	/*******************************************************************************************/
	/*                                       TESORERIA                                         */
	/*******************************************************************************************/
	/**
     * Funcion que Lista los documentos que se encuentran en tesoreria.
	 * Vista: factura.tesoreria.index
	 * Rol: tesoreria
     *
     * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
     */
    public function tesoreria(Request $request)
	{
		if (Auth::check()) { 
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			$documentos =  DB::table('documentos')
							->join('movimientos', 'documentos.id','=','movimientos.documento_id')
							->join('proveedors','documentos.proveedor_id','=','proveedors.id')
							->join('tipo_docs','documentos.tipoDoc_id','=','tipo_docs.id')
							->leftjoin('convenios','documentos.convenio_id','=','convenios.id')
							->leftjoin('referentes','convenios.referente_id','=','referentes.id')
							->select('proveedors.rut as rut',
									 'proveedors.name as nameProveedor',
									 'tipo_docs.name as tipoDoc',
									 'documentos.id as id',
									 'documentos.nDoc as nDoc',
									 'documentos.ordenCompra as ordenCompra',
									 'documentos.archivo as archivo',
									 'documentos.nomina as nomina',
									 'documentos.monto',
									 'referentes.name as referente',
									 'movimientos.estado as estado',
									 'movimientos.observacion as observacion')
							->selectRaw('DATE_FORMAT(documentos.fechaRecepcion, "%d-%m-%Y") as fechaRecepcion')
							->where([
									['documentos.establecimiento_id','=',$estab_user],
									['movimientos.active',1],
									['rut', 'LIKE', '%'.$request->get('searchRut').'%'],
									['tipo_docs.id', 'LIKE', '%'.$request->get('searchTipo').'%'],
									['nDoc', 'LIKE', '%'.$request->get('searchNdoc').'%'],
									])
							->whereIn('movimientos.estado',['TE'])		
							->orderBy('documentos.id','desc')->paginate(30)
							->appends('searchRut',$request->get('searchRut'))
							->appends('searchTipo',$request->get('searchTipo'))
							->appends('searchNdoc',$request->get('searchNdoc'));
			
			$tipos = TipoDoc::where([['active',1],['flujo',1]])->orderBy('name')->get();
			
			$tipoPagos = TipoPago::where('active',1)->orderBy('name')->get();
			$cuentas   = Cuenta::where('active',1)->orderBy('name')->get();
			
			return view('factura.tesoreria.index',compact('documentos','tipos','tipoPagos','cuentas'));
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Funcion que Guarda datos de pago de documentos
	 * Rol: tesoreria
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function movPago(Request $request) 
	{		
		if (Auth::check()) {
			$validator = validator::make($request->all(), [
				'tipoPago'     => 'required',
				'fechaPago'     => 'required',
				'sigfe'         => 'required|integer|min:0',
			]);	
			if ($validator->fails()) {
				return redirect('tesoreria')
							->withErrors($validator)
							->withInput();
			}
			else {
				
				//determina tipo de pago
				$tipoPago = TipoPago::find($request->tipoPago);
				
				if($tipoPago->entrega == 1) {
					$estado = 'EN';
				}
				else {
					$estado = 'FN';
				}
				//determina los documentos seleccionados
				$checks = $request->check_list;
			
				if(!empty($checks)) {
					foreach($checks as $check) {
						$documento = Documento::find($check);
						
						$fechaPago   = DateTime::createFromFormat('d-m-Y', $request->fechaPago);
						
						$documento->pago_operacion   = $request->nroOperacion;
						$documento->pago_tipo_id     = $request->tipoPago;
						$documento->pago_cuenta_id   = $request->cuenta;
						$documento->pago_fechaPago   = $fechaPago;
						$documento->pago_sigfe       = $request->sigfe;
						
						$documento->save();
						
						//genera movimieto de pago
						$mov = Movimiento::where([['documento_id',$documento->id],['active',1]])->first();
									
						//cambia estado de movimiento de activo a no activo
						$id = $mov->id; 
						$movimiento = Movimiento::find($id);
						$movimiento->active = 0;
						
						$movimiento->save();
						
						//guarda nuevo movimiento
						$movimientoNew = new Movimiento;
						
						$movimientoNew->documento_id = $documento->id;
						$movimientoNew->estado       = $estado;
						$movimientoNew->observacion  = $request->observacion;
						$movimientoNew->user_id      = Auth::user()->id;
							
						$movimientoNew->save();
						
						//almacena datos en ABEX
						if ($estado == 'EN') {
							try {
								$result = DB::connection('oracle')->update("UPDATE DOCUMENTOS SET ESTADO = 'EN', ESTADO_NOMBRE = 'En Tesoreria para Entrega', FECHA_MODIFICADO = '".$movimientoNew->created_at."' WHERE ID = ".$movimientoNew->documento_id);	
							}
							catch(Exception $e){}	
						}
						else {
							try {
								$result = DB::connection('oracle')->update("UPDATE DOCUMENTOS SET ESTADO = 'FN', ESTADO_NOMBRE = 'Entregado', FECHA_MODIFICADO = '".$movimientoNew->created_at."' WHERE ID = ".$movimientoNew->documento_id);	
							}
							catch(Exception $e){}	
						}	
						//fin datos en ABEX
					}	
				}
				return redirect('tesoreria')->with('message','pago');
			}
		}
		else {
			return view('auth/login');
		}
	}

	/**
     * Funcion que Lista los documentos que se encuentran en tesoreria para pago.
	 * Vista: factura.tesoreria.entrega
	 * Rol: tesoreria
     *
     * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
     */
    public function entrega(Request $request)
	{
		if (Auth::check()) { 
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			$documentos =  DB::table('documentos')
							->join('movimientos', 'documentos.id','=','movimientos.documento_id')
							->join('proveedors','documentos.proveedor_id','=','proveedors.id')
							->join('tipo_docs','documentos.tipoDoc_id','=','tipo_docs.id')
							->leftjoin('convenios','documentos.convenio_id','=','convenios.id')
							->leftjoin('referentes','convenios.referente_id','=','referentes.id')
							->leftjoin('tipo_pagos','documentos.pago_tipo_id','=','tipo_pagos.id')
							->select('proveedors.rut as rut',
									 'proveedors.name as nameProveedor',
									 'tipo_docs.name as tipoDoc',
									 'documentos.id as id',
									 'documentos.nDoc as nDoc',
									 'documentos.ordenCompra as ordenCompra',
									 'documentos.archivo as archivo',
									 'documentos.nomina as nomina',
									 'documentos.monto',
									 'documentos.pago_operacion as pago_operacion',
									 'documentos.pago_sigfe as pago_sigfe',
									 'tipo_pagos.name as tipoPago',
									 'referentes.name as referente',
									 'movimientos.estado as estado',
									 'movimientos.observacion as observacion')
							->selectRaw('DATE_FORMAT(documentos.fechaRecepcion, "%d-%m-%Y") as fechaRecepcion')
							->selectRaw('DATE_FORMAT(documentos.pago_fechaPago, "%d-%m-%Y") as fechaPago')
							->where([
									['documentos.establecimiento_id','=',$estab_user],
									['movimientos.active',1],
									['rut', 'LIKE', '%'.$request->get('searchRut').'%'],
									['tipo_docs.id', 'LIKE', '%'.$request->get('searchTipo').'%'],
									['nDoc', 'LIKE', '%'.$request->get('searchNdoc').'%'],
									['pago_sigfe', 'LIKE', '%'.$request->get('searchEgreso').'%'],
									])
							->whereIn('movimientos.estado',['EN'])		
							->orderBy('documentos.id','desc')->paginate(10)
							->appends('searchRut',$request->get('searchRut'))
							->appends('searchTipo',$request->get('searchTipo'))
							->appends('searchNdoc',$request->get('searchNdoc'))
							->appends('searchEgreso',$request->get('searchEgreso'));
			
			
			$tipos = TipoDoc::where([['active',1],['flujo',1]])->orderBy('name')->get();
			
			return view('factura.tesoreria.entrega',compact('documentos','tipos'));
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Funcion que Guarda datos de entrega de documentos
	 * Rol: tesoreria
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function movEntrega(Request $request) 
	{
		
		if (Auth::check()) {
			$validator = validator::make($request->all(), [
				'rut'    => 'required|string|max:150',
				'nombre' => 'required|string|max:150',
				'fechaEntrega' => 'required',
			]);	
			if ($validator->fails()) {
				return redirect('tesoreria/'.$request->documento_id.'/entrega/')
							->withErrors($validator)
							->withInput();
			}
			else {
				//determina los documentos seleccionados
				$checks = $request->check_list;
			
				if(!empty($checks)) {
					foreach($checks as $check) {
						$documento = Documento::find($check);

						$fechaEntrega = DateTime::createFromFormat('d-m-Y', $request->fechaEntrega);
										
						$documento->entrega_fecha  = $fechaEntrega;
						$documento->entrega_rut    = $request->rut;
						$documento->entrega_nombre = $request->nombre;
						
						$documento->save();
						
						//genera movimieto de pago
						$mov = Movimiento::where([['documento_id',$documento->id],['active',1]])->first();
									
						//cambia estado de movimiento de activo a no activo
						$id = $mov->id; 
						$movimiento = Movimiento::find($id);
						$movimiento->active = 0;
						
						$movimiento->save();
						
						//guarda nuevo movimiento
						$movimientoNew = new Movimiento;
						
						$movimientoNew->documento_id = $documento->id;
						$movimientoNew->estado       = 'FN';
						$movimientoNew->user_id      = Auth::user()->id;
							
						$movimientoNew->save();
						
						//almacena datos en ABEX
						try {
							$result = DB::connection('oracle')->update("UPDATE DOCUMENTOS SET ESTADO = 'FN', ESTADO_NOMBRE = 'Entregado', FECHA_MODIFICADO = '".$movimientoNew->created_at."' WHERE ID = ".$movimientoNew->documento_id);	
						}
						catch(Exception $e){}
						//fin datos en ABEX
					}	
				}	

				return redirect('tesoreria/entrega')->with('message','entrega');
			}
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
	 * Funcion que llama a Formulario de Ingreso de reporte SIGFE
	 * Vista: factura.tesoreria.sigfe
	 * Rol: tesoreria
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function ingresoSigfe(Request $request)
	{
		if (Auth::check()) {
			return view('factura.tesoreria.sigfe');
		}
		else {
			return view('auth/login');
		}	
	}
	
	/**
	 * Función de Accion de cargar documentos de SIGFE
	 * Vista: factura.tesoreria.respuestaSigfe
	 * Rol: tesoreria
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function uploadSigfe(Request $request)
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			//verifica que archivo de Pagos SIGFE existe
			if ($request->hasFile('archivo')) {
				$upload = request()->file('archivo');

				if(!$upload->isValid()) {
					return redirect('tesoreria/sigfe')
					->with('message','invalid')
					->withInput();
				}

				if($upload->getClientOriginalExtension() <> 'xls') {
					return redirect('tesoreria/sigfe')
					->with('message','extension')
					->withInput();
				}
			}
			
			//inicializa variables
			$respuestas = [];
			$cont   = 0;
			$error1 = 0;
			$error2 = 0;			
			$error3 = 0;
			
			//recorre documentos Recepcionados por ACEPTA
			if ($request->hasFile('archivo')) {
				$upload = request()->file('archivo');
				
				//carga archivo excel
				$rows = \Excel::load($upload, function($reader) {})->get();
				
				foreach ($rows as $key => $value) {
					try {
						/*Obtiene Numero de Documento*/
						$nDoc = $value->numero_factura;					
						/*Obtiene Monto*/
						$monto = $value->monto;
						//revisa si vienen campos completos vacios
						if(empty($value->rut) && empty($value->numero_factura) && empty($value->monto) && empty($value->folio) && empty($value->medio_pago)){
							continue;
						}
						//revisa si existen los campos
						if(empty($value->rut)){
							$texto = $value->rut.'::'.$value->numero_factura.'::'.$value->monto.'::Proveedor no ingresado::1::'.$value->medio_pago.'::'.$value->folio;
							$error1 = $error1 + 1;
							array_push($respuestas, $texto);
							continue;
						}
						if(empty($value->numero_factura)){
							$texto = $value->rut.'::'.$value->numero_factura.'::'.$value->monto.'::Nro de factura no ingresado::1::'.$value->medio_pago.'::'.$value->folio;
							$error1 = $error1 + 1;
							array_push($respuestas, $texto);
							continue;
						}
						if(empty($value->monto)){
							$texto = $value->rut.'::'.$value->numero_factura.'::'.$value->monto.'::Monto no ingresado::1::'.$value->medio_pago.'::'.$value->folio;
							$error1 = $error1 + 1;
							array_push($respuestas, $texto);
							continue;
						}
						if(empty($value->folio)){
							$texto = $value->rut.'::'.$value->numero_factura.'::'.$value->monto.'::Folio Sigfe no ingresado::1::'.$value->medio_pago.'::'.$value->folio;
							$error1 = $error1 + 1;
							array_push($respuestas, $texto);
							continue;
						}
						if(empty($value->medio_pago)){
							$texto = $value->rut.'::'.$value->numero_factura.'::'.$value->monto.'::Medio de pago no ingresado::1::'.$value->medio_pago.'::'.$value->folio;
							$error1 = $error1 + 1;
							array_push($respuestas, $texto);
							continue;
						}

						//revisa el tipo de dato 
						if(!is_numeric($value->folio)){
							$texto = $value->rut.'::'.$value->numero_factura.'::'.$value->monto.'::El campo folio debe ser numérico::1::'.$value->medio_pago.'::'.$value->folio;
							$error1 = $error1 + 1;
							array_push($respuestas, $texto);
							continue;
						}	

						if(!is_numeric($value->medio_pago)){
							$texto = $value->rut.'::'.$value->numero_factura.'::'.$value->monto.'::El campo medio de pago debe ser numérico::1::'.$value->medio_pago.'::'.$value->folio;
							$error1 = $error1 + 1;
							array_push($respuestas, $texto);
							continue;
						}	

						/*Obtiene Proveedor*/
						$emisor = $value->rut;
						$rut = explode("-", $emisor);
						$dv = $rut[1];
						$rut = str_ireplace('.','',$rut[0]);
						$rut = str_ireplace(' ','',$rut);
						
						$proveedor = Proveedor::where([['rut','=',$rut],['dv','=',$dv]])->first();
						
						if (is_null($proveedor)==false) {
							$proveedor_id   = $proveedor->id;
							$proveedor_name = $proveedor->name;
						}
						else {
							//imprimir documento no guardado
							$texto = $value->rut.'::'.$value->numero_factura.'::'.$value->monto.'::Proveedor no Existe::2::'.$value->medio_pago.'::'.$value->folio;
							$error2 = $error2 + 1;
							array_push($respuestas, $texto);
							continue;
						}
						
						/*Verifica si documento existe*/
						$documentoCount = DB::table('documentos')->where([['documentos.proveedor_id',$proveedor_id],['documentos.nDoc',$nDoc],['documentos.monto',$monto],['movimientos.estado', '<>', 'RE'],['movimientos.active',1]])->join('movimientos', 'documentos.id', '=', 'movimientos.documento_id')->count();
						if($documentoCount <> 1) {
							//imprimir documento no guardado
							if($documentoCount==0){
								$texto = $value->rut.'::'.$value->numero_factura.'::'.$value->monto.'::El documento no ha sido ingresado::3::'.$value->medio_pago.'::'.$value->folio;
								array_push($respuestas, $texto);
							}
							else {
								$texto = $value->rut.'::'.$value->numero_factura.'::'.$value->monto.'::Hay más de un documento con estos datos::3::'.$value->medio_pago.'::'.$value->folio;
								array_push($respuestas, $texto);
							}
							$error3 = $error3 + 1;
							continue;
						}

						/*Obtiene Fecha de pago   */
						$fechaPago = date('Y-m-d');
					
						/*guarda documento en DB*/
						$documento_id = DB::table('documentos')->select('documentos.id as id')->where([['documentos.proveedor_id',$proveedor_id],['documentos.nDoc',$nDoc],['documentos.monto',$monto],['movimientos.estado', '<>', 'RE'],['movimientos.active',1]])->join('movimientos', 'documentos.id', '=', 'movimientos.documento_id')->first();
						
						$documento = Documento::where([['id',$documento_id->id],['proveedor_id',$proveedor_id],['nDoc',$nDoc],['monto',$monto]])->first();
						
						if($documento->establecimiento_id<>$estab_user){
							$texto = $value->rut.'::'.$value->numero_factura.'::'.$value->monto.'::Documento no corresponde a este establecimiento::3::'.$value->medio_pago.'::'.$value->folio;
							$error3 = $error3 + 1;
							array_push($respuestas, $texto);
							continue;
						}

						$tipo    = $value->medio_pago;
						$tipoPago = TipoPago::where('id_sigfe','=',$tipo)->first();
						
						if (is_null($tipoPago)) {
							//imprimir documento no guardado
							$texto = $value->rut.'::'.$value->numero_factura.'::'.$value->monto.'::El tipo de pago no existe::1::'.$value->medio_pago.'::'.$value->folio;
							$error1 = $error1 + 1;
							array_push($respuestas, $texto);
							continue;
						}
					
							
						$documento->pago_fechaPago=$fechaPago;
						$documento->pago_sigfe=$value->folio;
						$mov = Movimiento::where([['documento_id',$documento->id],['active',1]])->first();
							
						if (empty($mov)) {
							$texto = $value->rut.'::'.$value->numero_factura.'::'.$value->monto.'::El estado del documento no corresponde::3::'.$value->medio_pago.'::'.$value->folio;
							$error3 = $error3 + 1;
							array_push($respuestas, $texto);
							continue;
						}
						
						//guarda movimiento
						if ( $mov->estado == 'TE') {
							$mov->active=0;
							$mov->save(); // cambia el estado active anterior a 0
						
							if($value->medio_pago == 1) { //TRANSFERENCIA
								$movimiento = new Movimiento;
								$movimiento->documento_id = $documento->id;
								$movimiento->estado = 'FN';
								//agrega información, en caso de que sea incluida en reporte
								if(empty($value->observacion)) {
									$movimiento->observacion = 'Carga de Datos - SIGFE';
								}
								else {
									$movimiento->observacion = 'Carga de Datos - SIGFE | '.substr($value->observacion,0,150);
								}
								$movimiento->user_id = Auth::user()->id;
								$documento->pago_tipo_id = 9;
								$documento->save();
								$movimiento->save();
								//almacena datos en ABEX
								try {
									$result = DB::connection('oracle')->update("UPDATE DOCUMENTOS SET ESTADO = 'FN', ESTADO_NOMBRE = 'Entregado', FECHA_MODIFICADO = '".$movimiento->created_at."' WHERE ID = ".$movimiento->documento_id);	
								}
								catch(Exception $e){}
								//fin almacena datos en ABEX
								$cont = $cont + 1;
							}
							else if($value->medio_pago == 3) { //CHEQUE
								$movimiento = new Movimiento;
								$movimiento->documento_id = $documento->id;
								$movimiento->estado = 'EN';
								//agrega información, en caso de que sea incluida en reporte
								if(empty($value->observacion)) {
									$movimiento->observacion = 'Carga de Datos - SIGFE';
								}
								else {
									$movimiento->observacion = 'Carga de Datos - SIGFE | '.substr($value->observacion,0,150);
								}
								$movimiento->user_id = Auth::user()->id;
								$documento->pago_tipo_id = 11;
								$documento->save();
								$movimiento->save();
								//almacena datos en ABEX
								try {
									$result = DB::connection('oracle')->update("UPDATE DOCUMENTOS SET ESTADO = 'EN', ESTADO_NOMBRE = 'En Tesoreria para Entrega', FECHA_MODIFICADO = '".$movimiento->created_at."' WHERE ID = ".$movimiento->documento_id);	
								}
								catch(Exception $e){}	
								//fin almacena datos en ABEX
								$cont = $cont + 1;
							} 
							else {
								$texto = $value->rut.'::'.$value->numero_factura.'::'.$value->monto.'::Medio de pago no corresponde::1::'.$value->medio_pago.'::'.$value->folio;
								$error1 = $error1 + 1;
								array_push($respuestas, $texto);
								continue;
							}	
						}
						else{
							$texto = $value->rut.'::'.$value->numero_factura.'::'.$value->monto.'::El estado del documento no corresponde::3::'.$value->medio_pago.'::'.$value->folio;
							$error3 = $error3 + 1;
							array_push($respuestas, $texto);
							continue;
						}
					} 
					catch (Exception $e) {
						$texto = $value->rut.'::'.$value->numero_factura.'::'.$value->monto.'::Datos mal ingresados::1::'.$value->medio_pago.'::'.$value->folio; 
						$error3 = $error3 + 1;
						array_push($respuestas, $texto);
						continue;
					}
				}	
			}			
			Session::put('respuestas', $respuestas);
			return view('factura.tesoreria.respuestaSigfe',compact('respuestas','cont','error1','error2','error3'));
		}
		else {
			return view('auth/login');
		}		
	}	
	/**
	 * Funcion que genera Excel de la respuesta SIGFE
	 * Rol: tesoreria
	 *
     * @param  \Illuminate\Http\Request  $request	 
	 * @return list lista de documentos incluidos en SIGDOC (Todos los Establecimientos)
	 */
	public function excelRespuestaSIGFE(Request $request) 
	{
		try {		
			if (Auth::check()) {
				if (Session::has('respuestas')){

					ini_set("memory_limit", -1);
					ini_set('max_execution_time', 300);
					$respuestas= Session::get('respuestas');//Recupera un objeto 
					//Session::forget('respuestas'); //Elimina la variable en session	
					//crea array con información de consulta
					$documentosArray[] = ['rut','numero_factura','monto','folio','medio_pago','error'];
					
					foreach($respuestas as $respuesta){
						$respuesta = explode("::", $respuesta);
						/*
						$respuesta[0]; //rut proveedor
						$respuesta[1];//nro factura
						$respuesta[2]; //monto
						$respuesta[3]; //error
						$respuesta[4]; //tipo_error
						$respuesta[5]; //medio de pago
						$respuesta[6]; //folio 			
						*/	
						$rut = $respuesta[0]; //rut proveedor
						$numero_factura = $respuesta[1];//nro factura
						$monto = $respuesta[2]; //monto
						$error = $respuesta[3]; //error
						$medio_pago = $respuesta[5]; //medio de pago
						$folio = $respuesta[6]; //folio 
						$documentosArray[] = [ $rut,
		                                 	   $numero_factura,
											   $monto,
											   $folio,
											   $medio_pago,
											   $error
											   ];
					}
					//Genera archivo Excel
					Excel::create('Resultado_CM_'.date("d-m-Y_H:i"), function($excel) use($documentosArray) {
		 				$excel->sheet('Documentos', function($sheet) use($documentosArray) {
							$sheet->fromArray($documentosArray,null,'A1',true,false);
			 			});
					})->export('xls');

				}
				else{
					return view('auth/login');
				}

			}
			else {
				return view('auth/login');
			}
		} catch (Exception $e) {
			return view('auth/login');
		}		        
	}
	
	/*******************************************************************************************/
	/*                                       ADMINISTRADOR                                     */
	/*******************************************************************************************/
	/**
	 * Funcion Para Administración de Documentos Fuera de Flujo
	 * Vista: factura.administrador.index
	 * Rol: Administrador
	 *
     * @param  \Illuminate\Http\Request  $request	 
	 * @return \Illuminate\Http\Response
	 */
	public function adminDocumentos(Request $request) 
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;
			
			$tipos = TipoDoc::where([['flujo',1],['active',1]])->orderBy('name')->get();
			
			$documentos = DB::table('documentos')
							->join('movimientos', 'documentos.id','=','movimientos.documento_id')
							->join('proveedors','documentos.proveedor_id','=','proveedors.id')
							->join('tipo_docs','documentos.tipoDoc_id','=','tipo_docs.id')
							->orderBy('documentos.id','desc')
							->select('proveedors.rut as rut',
									 'proveedors.name as nameProveedor',
									 'tipo_docs.name as tipoDoc',
									 'documentos.id as id',
									 'documentos.nDoc as nDoc',
									 'documentos.archivo as archivo',
									 'documentos.nomina as nomina',
									 'documentos.monto',
									 'documentos.pago_operacion as pago_operacion',
									 'documentos.pago_sigfe as pago_sigfe',
									 'movimientos.estado as estado')
							->selectRaw('DATE_FORMAT(documentos.fechaEmision, "%d-%m-%Y") as fechaEmision')
							->selectRaw('DATE_FORMAT(documentos.fechaRecepcion, "%d-%m-%Y") as fechaRecepcion')
							->where([['documentos.establecimiento_id',$estab_user],
									 ['movimientos.active',1],
									 ['rut', 'LIKE', '%'.$request->get('searchRut').'%'],
									 ['tipo_docs.id', 'LIKE', '%'.$request->get('searchTipo').'%'],
									 ['nDoc', 'LIKE', '%'.$request->get('searchNdoc').'%']])
							->whereNotIn('movimientos.estado',['OP','NP'])
							->orderBy('documentos.id','desc')->paginate(10)
							->appends('searchRut',$request->get('searchRut'))
							->appends('searchTipo',$request->get('searchTipo'))
							->appends('searchNdoc',$request->get('searchNdoc'));
			
			return view('factura.administrador.index',compact('tipos','documentos'));	
		}
		else {
			return view('auth/login');
		}
	} 
	
	/**
	 * Funcion que genera Formulario de reversa ultimo movimiento del documento agregando un nuevo movimiento
	 * Vista: factura.administrador.reversar
	 * Rol: Administrador
	 *
     * @param  \Illuminate\Http\Request  $request	 
	 * @param  int $id Identificador de Documentos
	 * @return \Illuminate\Http\Response
	 */
	public function reversar(Request $request, $id) 
	{
		if (Auth::check()) {
			$documento 	= Documento::find($id);
			$proveedor 	= Proveedor::find($documento->proveedor_id);
			$tipo 	 	= TipoDoc::find($documento->tipoDoc_id);
			$movActual 	= Movimiento::where([['active',1],['documento_id',$id]])->first()->estado;
			$movAnt		= Movimiento::where([['active',0],['documento_id',$id]])->orderBy('created_at','DESC')->first()->estado;
				
			if( Auth::user()->isRole('Administrador') ) { 
				return view('factura.administrador.reversar',compact('documento','proveedor','tipo','movActual','movAnt'));
			}
			else {
				return view('home');
			}
		}
		else {
			return view('auth/login');
		}
	}

	/**
	 * Funcion de Acción de reversa ultimo movimiento del documento agregando un nuevo movimiento
	 * Rol: Administrador
	 *
     * @param  \Illuminate\Http\Request  $request	 
	 * @return \Illuminate\Http\Response
	 */
	public function accionReversar(Request $request) 
	{
		if (Auth::check()) {
			$id = $request->documento_id;
			
			//determina el penultimo movimiento
			$movAnt		= Movimiento::where([['active',0],['documento_id',$id]])->orderBy('created_at','DESC')->first();
			
			//cambia estado de movimiento de activo a no activo
			$movActual 	= Movimiento::where([['active',1],['documento_id',$id]])->first();
			$movimiento = Movimiento::find($movActual->id);
			$movimiento->active = 0;
				
			$movimiento->save();
			
			//crea un nuevo movimiento igual al anterior
			$movimientoNew = new Movimiento;
				
			$movimientoNew->documento_id = $id;
			$movimientoNew->estado = $movAnt->estado;
			$movimientoNew->observacion = str_replace(PHP_EOL,"<br>",$request->observacion);
			$movimientoNew->user_id = Auth::user()->id;
				
			$movimientoNew->save();
			
			//retorna pantalla anterior
			return redirect('administrador/documentos')->with('message','reversa');
		}
		else {
			return view('auth/login');
		}	
	}	
	
    /*******************************************************************************************/
	/*                                   MODULOS GENERICOS                                     */
	/*******************************************************************************************/
	/**
     * Funcion que Muestra validadores adjuntos.
	 * Vista: factura.validadores
	 * Rol: None
     *
     * @param  int  $id Identificador de Documento
	 * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function validadores(Request $request, $id)
    {
        if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			$documento = Documento::find($id);
			
			if ( $documento->establecimiento_id == $estab_user ) {	
			
				$proveedor = Proveedor::find($documento->proveedor_id);
				
				$tipo = TipoDoc::find($documento->tipoDoc_id);
				
				$movimiento = Movimiento::where([['documento_id',$id],['active',1]])->first();
				
				$validadores = DB::table('validadors')
							   ->join('documento_validadors', 'validadors.id','documento_validadors.validador_id')
							   ->where([['documento_validadors.documento_id','=',$id],
										['documento_validadors.active','=','1']])
							   ->select('validadors.name as name',
										'validadors.id as validador_id',
										'documento_validadors.archivo as archivo')
							   ->orderBy('validadors.name')->get();
							   
				//determina los id de los documentos adjuntos
				$vectorId = [];
				foreach($validadores as $validador) {
					$vectorId[] = $validador->validador_id;
				}
				
				//consulta por validadores no adjuntos
				$validadores2 = DB::table('validadors')
							   ->join('convenio_validador', 'validadors.id','=','convenio_validador.validador_id')
							   ->where('convenio_validador.convenio_id',$documento->convenio_id)
							   ->whereNotIn('validadors.id',$vectorId)
							   ->select('validadors.name as name')
							   ->orderBy('validadors.name')->get();
				
				return view('factura.validadores',compact('documento','proveedor','tipo','validadores','validadores2','movimiento'));
			}
			else {
				return view('home');
			}
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * funcion que Edita Documentos.
	 * Vista: factura.edit
	 * Rol: oficinaPartes - secretariaConvenios - Administrador
     *
     * @param  int  $id Identificador de Documento
	 * @param  int  $flujo Flujo que edita documento (1 - oficina de Partes ; 2 - Secretaria de Convenios)
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id, $flujo)
    {
        if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			$documento = Documento::find($id);
			
			//determina si el documento esta en oficina de parte (flujo = 1) o secretaria de convenios (flujo = 2)
			$aux = 0; 
			if ( Auth::user()->isRole('Oficina de Partes') && $flujo == 1 && $documento->establecimiento_id == $estab_user ) {
				$aux = Movimiento::where([['documento_id',$documento->id],['estado','OP'],['active','1']])->count();
			}
			elseif ( Auth::user()->isRole('Secretaria de Convenios') && $flujo == 2 && $documento->establecimiento_id == $estab_user ) {
				$aux = Movimiento::where([['documento_id',$documento->id],['estado','NP'],['active','1']])->count();
			}
			elseif ( Auth::user()->isRole('Administrador') && $documento->establecimiento_id == $estab_user ) {
				$aux = 1;
			}
			
			if ( $aux == 1  ) {		
				$proveedor = Proveedor::find($documento->proveedor_id);
				$tipos = TipoDoc::where([['active',1],['flujo',1]])->orderBy('name')->get();
				$tipoDoc = TipoDoc::find($documento->tipoDoc_id);
				
				return view('factura.edit',compact('documento','proveedor','tipos','flujo','tipoDoc'));
			}
			else {
				return view('home');
			}	
		}
		else {
			return view('auth/login');
		}
    }
	
	/**
     * Funcion que actualiza Datos de Documentos.
	 * Rol: oficinaPartes - secretariaConvenios - Administrador
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id Identificador de Documento
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (Auth::check()) {
			// validate requeridos
			$validator = validator::make($request->all(), [
				'nDoc' => 'required|integer|min:0',
				'tipo' => 'required',
				'facAsociada' => 'nullable|integer|min:0',
				'fechaRecepcion' => 'required',
				'fechaEmision' => 'required',
				'fechaVencimiento' => 'required',
				'monto' => 'required|integer|min:0',
				'ordenCompra' => 'nullable|string|max:15',
				"archivo" => "nullable|mimes:pdf|max:10024",
			]);
			
			//determina formulario segun flujo
			if ( $request->flujo == 1 ) { //oficina de partes
				$modulo	= 'oficinaPartes/';
			}
			elseif ( $request->flujo == 2 ) { //secreataria de convenios
				$modulo	= 'secretariaConvenios/';
			}
			elseif ( $request->flujo == 0 ) { //administrador
				$modulo	= 'administrador/';
			}
			
			if ($validator->fails()) {
				return redirect($modulo.$id.'/edit/'.$request->flujo)
							->withErrors($validator)
							->withInput();
			}
			else {
				//tipo de documento
				$tipoExplode = explode("-",$request->tipo);
				$tipoValue   = $tipoExplode[0];
				
				//verifica que documento no exista
				$valor = Documento::where([['nDoc',$request->input('nDoc')],['proveedor_id',$request->input('proveedor_id')],['tipoDoc_id',$tipoValue]])->first();
				if ($valor != null && $valor->id != $id) {
					return redirect($modulo.$id.'/edit/'.$request->flujo)->with('message','documento')->withInput();
				}
				
				//adjunta archivo
				$archivoName = null;
				
				if ($request->hasFile('archivo')) {
					$archivo = $request->file('archivo');
					$archivoName = 'd'.time().$archivo->getClientOriginalName();
					
					//guarda archivo
					$request->file('archivo')->storeAs('public',$archivoName);
				}

				//formatea fechas
				$fechaEmision = DateTime::createFromFormat('d-m-Y', $request->fechaEmision);
				$fechaRecepcion = DateTime::createFromFormat('d-m-Y', $request->fechaRecepcion);
				$fechaVencimiento = DateTime::createFromFormat('d-m-Y', $request->fechaVencimiento);
				
							
				//guarda datos de documento
				$documento = Documento::find($id);
				
				$documento->tipoDoc_id = $tipoValue;
				$documento->nDoc = $request->nDoc;
				$documento->facAsociada = $request->facAsociada;
				$documento->fechaEmision = $fechaEmision;
				$documento->fechaRecepcion = $fechaRecepcion;
				$documento->fechaVencimiento = $fechaVencimiento;
				$documento->monto = $request->monto;
				$documento->ordenCompra = $request->ordenCompra;
				if($archivoName != null) {
					$documento->archivo = Storage::url($archivoName);
				}
				$documento->user_id = Auth::user()->id;
				
				$documento->save();
				
				if ($request->flujo == 1) { //edicion solicitada por oficina de partes
					return redirect('oficinaPartes/nomina')->with('message','update');
				}
				elseif ($request->flujo == 2) {
					return redirect('secretariaConvenios')->with('message','update');
				}
				elseif ($request->flujo == 0) {
					return redirect('administrador/documentos')->with('message','update');
				}
			}			
		}
		else {
			return view('auth/login');
		}
    }
	
	/**
     * Funcion que Llama al formulario de rechazo
	 * Vista: factura.rechazar
	 * Rol: oficinaPartes - secretariaConvenios - convenios - referenteTecnico - contabilidad
     *
     * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id Identificador de Documentos
	 * @param  int  $flujo Flujo que edita documento (1 - oficina de Partes ; 2 - Secretaria de Convenios)
     * @return \Illuminate\Http\Response
     */
    public function rechazar(Request $request, $id, $flujo)
    {
        if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			$documento = Documento::find($id);
			
			$mov = Movimiento::where([['documento_id',$id],['active',1]])->first(); 
			
			if ( Auth::user()->isRole('Oficina de Partes') && $flujo == 1 && $mov->estado != 'OP') { //documento no disponible para rechazar por oficina de partes
				return view('home');
			}
			
			if ( Auth::user()->isRole('Secretaria de Convenios') && $flujo == 2 && $mov->estado != 'NP') { //documento no disponible para rechazar por secreataria de convenios
				return view('home');
			}
			
			if ( Auth::user()->isRole('Convenios') && $flujo == 3) { //documento no disponible para rechazar por oficina convenios
				if ( $mov->estado != 'CV' && $mov->estado != 'DV' ) {
					return view('home');
				}
			}
			
			if ( Auth::user()->isRole('Referente Tecnico') && $flujo == 4) { //documento no disponible para rechazar por referente tecnico
				if ( $mov->estado != 'VB' && $mov->estado != 'DR' && $mov->estado != 'DO' ) {
					return view('home');
				}	
			}
			
			if ( Auth::user()->isRole('Contabilidad') && $flujo == 5 && $mov->estado != 'DE') { //documento no disponible para rechazar por referente tecnico
				return view('home');
			}
			
			if ( $documento->establecimiento_id == $estab_user ) {	
				
				$proveedor = Proveedor::find($documento->proveedor_id);
				
				$tipo = TipoDoc::find($documento->tipoDoc_id);
				
				return view('factura.rechazar',compact('documento','proveedor','tipo','flujo'));
			}
			else {
				return view('home');
			}	
		}
		else {
			return view('auth/login');
		}
    }
	
	/**
     * Funcion que genera accion de Rechazo de documentos
	 * Rol: oficinaPartes - secretariaConvenios - convenios - referenteTecnico - contabilidad
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function movRechazar(Request $request) 
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;
						
			$mov = Movimiento::where([['documento_id',$request->documento_id],['active',1]])->first();
			
			//cambia estado de movimiento de activo a no activo
			$id = $mov->id; 
			$movimiento = Movimiento::find($id);
			$movimiento->active = 0;
			
			$movimiento->save();
			
			//guarda nuevo movimiento
			$movimientoNew = new Movimiento;
			
			$movimientoNew->documento_id = $request->documento_id;
			$movimientoNew->estado = 'RE';
			$movimientoNew->observacion = str_replace(PHP_EOL,"<br>",$request->observacion);
			$movimientoNew->user_id = Auth::user()->id;
				
			$movimientoNew->save();
			
			//almacena datos en ABEX
			try {
				$result = DB::connection('oracle')->update("UPDATE DOCUMENTOS SET ESTADO = 'RE', ESTADO_NOMBRE = 'Rechazado', FECHA_MODIFICADO = '".$movimientoNew->created_at."' WHERE ID = ".$movimientoNew->documento_id);	
			}
			catch(Exception $e){}
			//fin datos en ABEX
			
			//envia mail de rechazo a Contabilidad y Convenios
			//try {
				//determina usuarios pertenecientes Contabilidad o Convenios
				$users = DB::table('establecimiento_user')
						->join('role_user', 'establecimiento_user.user_id','role_user.user_id')
						->join('users','establecimiento_user.user_id','users.id')
						->select('establecimiento_user.user_id as id')
						->where('establecimiento_user.establecimiento_id',$estab_user)
						->where('users.active',1)
						->whereIn('role_user.role_id',[8,5])->get(); 
				//determina documento
				$documento = Documento::find($request->documento_id);
				
				//determina proveedor
				$proveedor = Proveedor::find($documento->proveedor_id);
				
				foreach ($users as $user) {
					//selecciona usuario
					$aux = User::find($user->id);
					$aux->notify(new RechazoMail($proveedor->name, $documento->nDoc));
				}
			//}
			//catch(Exception $e){}
			
			if ($request->flujo == 1) { //secretaria de convenios
				return redirect('oficinaPartes/nomina')->with('message','rechazo');
			}
			elseif ($request->flujo == 2) { //secretaria de convenios
				return redirect('secretariaConvenios')->with('message','rechazo');
			}
			elseif ($request->flujo == 3) { //convenios
				return redirect('oficinaConvenios/vistosBuenos')->with('message','rechazo');
			}
			elseif ($request->flujo == 4) { //convenios
				return redirect('referenteTecnico')->with('message','rechazo');
			}
			elseif ($request->flujo == 5) { //convenios
				return redirect('contabilidad')->with('message','rechazo');
			}
			
		}
		else {
			return view('auth/login');
		}
	}

	/**
     * Funcion que llama a Selector de Campos para Reporte de documentos
	 * Vista: factura.reporte
	 * Rol: None
     *
     * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
     */
	public function reporte(Request $request) 
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;
			
			$tipos = TipoDoc::where([['flujo',1],['active',1]])->orderBy('name')->get();
			
			$referentes  = Referente::where([['establecimiento_id',$estab_user],['active',1]])->orderBy('name')->get();
			
			return view('factura.reporte',compact('tipos','referentes'));	
		}
		else {
			return view('auth/login');
		}
	}

	/**
	 * Funcion de Resultado reporte según filtros definidos por el usuario
	 * Vista: factura.resultado
	 * Rol: None
	 *
     * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function resultado(Request $request) 
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab   = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab      = $user->establecimientos()->first()->id;
			
			$documentos =  DB::table('documentos')
							->join('movimientos', 'documentos.id','=','movimientos.documento_id')
							->join('proveedors','documentos.proveedor_id','=','proveedors.id')
							->join('tipo_docs','documentos.tipoDoc_id','=','tipo_docs.id')
							->join('establecimientos','documentos.establecimiento_id','=','establecimientos.id')
							->leftjoin('tipo_pagos','documentos.pago_tipo_id','=','tipo_pagos.id')
							->leftjoin('convenios','documentos.convenio_id','=','convenios.id')
							->leftjoin('referentes','convenios.referente_id','=','referentes.id')
							->orderBy('documentos.id','desc')
							->select('proveedors.rut as rut',
									 'proveedors.name as nameProveedor',
									 'tipo_docs.name as tipoDoc',
									 'documentos.id as id',
									 'documentos.nDoc as nDoc',
									 'documentos.fechaRecepcion as fechaRecepcion',
									 'documentos.ordenCompra as ordenCompra',
									 'documentos.archivo as archivo',
									 'documentos.nomina as nomina',
									 'documentos.monto',
									 'documentos.pago_operacion as pago_operacion',
									 'documentos.pago_sigfe as pago_sigfe',
									 'establecimientos.name as establecimiento',
									 'convenios.identificador as convenio',
									 'referentes.name as referente',
									 'tipo_pagos.name as tipoPago',
									 'movimientos.estado as estado',
									 'movimientos.observacion as observacion')
							->selectRaw('DATE_FORMAT(documentos.fechaRecepcion, "%d-%m-%Y") as fechaRecepcion')
							->selectRaw('DATE_FORMAT(documentos.pago_fechaPago, "%d-%m-%Y") as fechaPago')
							->where([['movimientos.active',1],['documentos.establecimiento_id',$estab]]);
			
			if( $request->get('proveedor') != null ) {
				//determina el ID del proveedor
				$explode = explode("-",$request->get('proveedor'));
				$rut = $explode[0];
				
				$documentos = $documentos->where('proveedors.rut',$rut);
			}
			
			if( $request->get('nomDoc') != null ) {
				$documentos = $documentos->where('documentos.nDoc',$request->get('nomDoc'));
			}
			
			if( $request->get('nomina') != null ) {
				$documentos = $documentos->where('documentos.nomina',$request->get('nomina'));
			}
			
			if( $request->get('tipo') != null ) {
				$documentos = $documentos->where('documentos.tipoDoc_id',$request->get('tipo'));
			}
			
			if( $request->get('monto') != null ) {
				$documentos = $documentos->where('documentos.monto',$request->get('monto'));
			}

			if( $request->get('ordenCompra') != null ) {
				$documentos = $documentos->where('documentos.ordenCompra',$request->get('ordenCompra'));
			}
			
			if( $request->get('referente') != null ) {
				$documentos = $documentos->where('convenios.referente_id',$request->get('referente'));
			}
			
			if( $request->get('desde') != null ) {
				//formatea fechas
				$fecha = DateTime::createFromFormat('d-m-Y H:i:s', $request->get('desde')." 00:00:00");
				
				$documentos = $documentos->where('documentos.fechaRecepcion','>=',$fecha);
			}
			
			if( $request->get('hasta') != null ) {
				//formatea fechas
				$fecha = DateTime::createFromFormat('d-m-Y H:i:s', $request->get('hasta')." 23:59:59");
								
				$documentos = $documentos->where('documentos.fechaRecepcion','<=',$fecha);
			}
			
			if( $request->get('estado') != null ) {
				$documentos = $documentos->where('movimientos.estado',$request->get('estado'));
			}
			
			if( $request->get('devengo') != null ) {
				
				if( $request->get('devengo') == 1 ) {
					$documentos = $documentos->whereNotNull('documentos.devengo_clasificador_id');
				}
				else {
					$documentos = $documentos->whereNull('documentos.devengo_clasificador_id');
				}
			}
			$documentos	= $documentos->paginate(10)
			->appends('proveedor',$request->get('proveedor'))
			->appends('nomDoc',$request->get('nomDoc'))
			->appends('nomina',$request->get('nomina'))
			->appends('tipo',$request->get('tipo'))
			->appends('monto',$request->get('monto'))
			->appends('ordenCompra',$request->get('ordenCompra'))
			->appends('referente',$request->get('referente'))
			->appends('desde',$request->get('desde'))
			->appends('hasta',$request->get('hasta'))
			->appends('estado',$request->get('estado'))
			->appends('devengo',$request->get('devengo'));
			
			//parametros de consulta
			$proveedor       = $request->get('proveedor');
			$nomDoc          = $request->get('nomDoc');
			$nomina          = $request->get('nomina');
			$tipo            = $request->get('tipo');
			$monto           = $request->get('monto');
			$ordenCompra     = $request->get('ordenCompra');
			$referente       = $request->get('referente');
			$desde           = $request->get('desde');
			$hasta           = $request->get('hasta');
			$estado          = $request->get('estado');
			$devengo         = $request->get('devengo');
			
			return view('factura.resultado',compact('documentos','proveedor','nomina','nomDoc','monto','ordenCompra','tipo','referente','desde','hasta','estado','devengo'));
		}
		else {
			return view('auth/login');
		}	
	}

	/**

	 * Funcion de Resultado reporte según filtros definidos por el usuario en formato excel
	 * Rol: None
	 *
     * @param  \Illuminate\Http\Request  $request
	 * @return list lista de documentos que coinciden con la busqueda
	 */
	public function excel(Request $request) 
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab   = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab      = $user->establecimientos()->first()->id;

			//inicializa variables para aumentar limite de tiempo de espera
			ini_set("memory_limit", -1);
			ini_set('max_execution_time', 300);
			
			$documentos =  DB::table('documentos')
							->join('movimientos', 'documentos.id','=','movimientos.documento_id')
							->join('proveedors','documentos.proveedor_id','=','proveedors.id')
							->join('tipo_docs','documentos.tipoDoc_id','=','tipo_docs.id')
							->join('establecimientos','documentos.establecimiento_id','=','establecimientos.id')
							->leftjoin('tipo_pagos','documentos.pago_tipo_id','=','tipo_pagos.id')
							->leftjoin('convenios','documentos.convenio_id','=','convenios.id')
							->leftjoin('referentes','convenios.referente_id','=','referentes.id')
							->orderBy('documentos.id','desc')
							->select('proveedors.rut as rut',
									 'proveedors.name as nameProveedor',
									 'tipo_docs.name as tipoDoc',
									 'documentos.id as id',
									 'documentos.nDoc as nDoc',
									 'documentos.fechaRecepcion as fechaRecepcion',
									 'documentos.ordenCompra as ordenCompra',
									 'documentos.archivo as archivo',
									 'documentos.nomina as nomina',
									 'documentos.monto',
									 'documentos.pago_operacion as pago_operacion',
									 'documentos.pago_sigfe as pago_sigfe',
									 'establecimientos.name as establecimiento',
									 'convenios.identificador as convenio',
									 'referentes.name as referente',
									 'tipo_pagos.name as tipoPago',
									 'movimientos.estado as estado',
									 'movimientos.observacion as observacion')
							->selectRaw('DATE_FORMAT(documentos.fechaEmision, "%d-%m-%Y") as fechaEmision')
							->selectRaw('DATE_FORMAT(documentos.fechaRecepcion, "%d-%m-%Y") as fechaRecepcion')
							->selectRaw('DATE_FORMAT(documentos.pago_fechaPago, "%d-%m-%Y") as fechaPago')
							->where([['movimientos.active',1],['documentos.establecimiento_id',$estab]]);
			if( $request->get('proveedor') != null ) {
				//determina el ID del proveedor
				$explode = explode("-",$request->get('proveedor'));
				$rut = $explode[0];
				
				$documentos = $documentos->where('proveedors.rut',$rut);
			}
			
			if( $request->get('nomDoc') != null ) {
				$documentos = $documentos->where('documentos.nDoc',$request->get('nomDoc'));
			}
			
			if( $request->get('nomina') != null ) {
				$documentos = $documentos->where('documentos.nomina',$request->get('nomina'));
			}
			
			if( $request->get('tipo') != null ) {
				$documentos = $documentos->where('documentos.tipoDoc_id',$request->get('tipo'));
			}
			
			if( $request->get('monto') != null ) {
				$documentos = $documentos->where('documentos.monto',$request->get('monto'));
			}

			if( $request->get('ordenCompra') != null ) {
				$documentos = $documentos->where('documentos.ordenCompra',$request->get('ordenCompra'));
			}
			
			if( $request->get('referente') != null ) {
				$documentos = $documentos->where('convenios.referente_id',$request->get('referente'));
			}
			
			if( $request->get('desde') != null ) {
				//formatea fechas
				$fecha = DateTime::createFromFormat('d-m-Y H:i:s', $request->get('desde')." 00:00:00");
				
				$documentos = $documentos->where('documentos.fechaRecepcion','>=',$fecha);
			}
			
			if( $request->get('hasta') != null ) {
				//formatea fechas
				$fecha = DateTime::createFromFormat('d-m-Y H:i:s', $request->get('hasta')." 23:59:59");
								
				$documentos = $documentos->where('documentos.fechaRecepcion','<=',$fecha);
			}
			
			if( $request->get('estado') != null ) {
				$documentos = $documentos->where('movimientos.estado',$request->get('estado'));
			}
			
			if( $request->get('devengo') != null ) {
				
				if( $request->get('devengo') == 1 ) {
					$documentos = $documentos->whereNotNull('documentos.devengo_clasificador_id');
				}
				else {
					$documentos = $documentos->whereNull('documentos.devengo_clasificador_id');
				}
			}
			$documentos	= $documentos->paginate(5000);
			
			//crea array con información de consulta
			$documentosArray[] = ['Proveedor','Tipo de Documento','Nro de Documento','Nomina','Monto','Fecha de Emisión','Fecha de Recepción','Referente Técnico','Convenio','Establecimiento','Estado'];
			
			foreach ($documentos->chunk(10) as $chunks) {
				foreach ($chunks as $documento)	{
					$proveedor = $documento->rut.' - '.$documento->nameProveedor;
					$tipoDoc   = $documento->tipoDoc;
					$numDoc    = $documento->nDoc;
					$nomina    = $documento->nomina;
					$monto     = $documento->monto;
					$fechaEmision  = $documento->fechaEmision;
					$fechaRecepcion  = $documento->fechaRecepcion;
					$referente       = $documento->referente;
					$convenio        = $documento->convenio;
					$establecimiento = $documento->establecimiento;
					switch ($documento->estado) {
						case 'OP': $estado =  "Ingresado Oficina de Partes"; break;
						case 'NP': $estado =  "Recepción Secretaría de Convenios"; break;	
						case 'CV': $estado =  "Asignar a Referente Técnico"; break;
						case 'VB': $estado =  "Referente Técnico - Por Validar"; break;
						case 'RT': $estado =  "Jefe Referente Técnico - Por Validar"; break;
						case 'RC': $estado =  "Documento Validado por Referente Técnico"; break;
						case 'DE': $estado =  "En Contabilidad"; break;
						case 'TE': $estado =  "En Tesorería para Pago"; break;
						case 'EN': $estado =  "En Tesorería para Entrega"; break;
						case 'DV': $estado =  "Devuelto Referente Técnico a Convenios"; break;
						case 'DR': $estado =  "Devuelto Jefe R.T. a Referente Técnico"; break;	 
						case 'DO': $estado =  "Devuelto Convenios a Referente Técnico"; break;
						case 'DC': $estado =  "Devuelto Contabilidad a Convenios"; break;
						case 'FN': $estado =  "Entregado"; break;
						case 'RE': $estado =  "Rechazado"; break;													
					}
					$documentosArray[] = [ $proveedor,
										$tipoDoc,
										$numDoc,
										$nomina,
										$monto,
										$fechaEmision,
										$fechaRecepcion,
										$referente,
										$convenio,
										$establecimiento,
										$estado ];
				}
			}
			
			//Genera archivo Excel
			Excel::create('Archivo', function($excel) use($documentosArray) {
 				$excel->sheet('Documentos', function($sheet) use($documentosArray) {
					$sheet->fromArray($documentosArray,null,'A1',true,false);
	 			});
			})->export('xls');
		}
		else {
			return view('auth/login');
		}	
	}
	
	/**
     * Funcion que Muestra bitacora de documentos.
	 * Vista: factura.bitacora
	 * Rol: None
     *
     * @param  int  $id Identificador de Documentos
	 * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bitacora(Request $request, $id)
    {
        if (Auth::check()) {
			$documento = Documento::find($id);
			
			$proveedor = Proveedor::find($documento->proveedor_id);
			
			$tipoDoc = TipoDoc::find($documento->tipoDoc_id);
			
			if ($documento->pago_tipo_id != null) {
				$tipoPago = TipoPago::find($documento->pago_tipo_id);
			}
			else {
				$tipoPago = null;
			}
			
			if ($documento->pago_cuenta_id != null) {
				$cuenta = Cuenta::find($documento->pago_cuenta_id);
			}
			else {
				$cuenta = null;
			}
			
			if ($documento->devengo_clasificador_id != null) {
				$item = Clasificador::find($documento->devengo_clasificador_id);
			}
			else {
				$item = null;
			}
			$movimientos = DB::table('movimientos')
						   ->join('users', 'users.id','=','movimientos.user_id')
						   ->where('movimientos.documento_id',$id)
						   ->select('movimientos.estado as estado',
									'movimientos.observacion as observacion',
									'users.name as user')
						   ->selectRaw('DATE_FORMAT(movimientos.created_at, "%d-%m-%Y") as fechaCreacion')
						   ->selectRaw('DATEDIFF(movimientos.updated_at, movimientos.created_at) as diferencia')
						   ->orderBy('movimientos.id')
						   ->get();
			
			return view('factura.bitacora',compact('documento','proveedor','tipoDoc','movimientos','tipoPago','cuenta','item'));
			
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Funcion que Muestra la Memo de Referente Tecnico en formato PDF
	 * Vista: factura.memoPdf
	 * Rol: None
     *
	 * @param  int  $id Identificador de Documentos 
     * @return \Illuminate\Http\Response
     */
	public function memoPdf($id) 
	{
		if (Auth::check()) {
			try {	
				$documento  = Documento::find($id);
				$tipoDoc    = TipoDoc::find($documento->tipoDoc_id);
				$proveedor  = Proveedor::find($documento->proveedor_id);
				$movimiento = Movimiento::where([['documento_id',$id],['estado','RC']])->latest()->first();
				$user       = User::find($movimiento->user_id);
				$convenio   = Convenio::find($documento->convenio_id);
				$referente  = Referente::find($convenio->referente_id);
				
				//determina el receptor del memo
				$fechaMov   = DateTime::createFromFormat('Y-m-d H:i:s', $movimiento->created_at);
				$fechaMov->setTime(0, 0);
				
				$firmante   = Firmante::where([['establecimiento_id',$documento->establecimiento_id],
											   ['memo_id',1],
											   ['active',1],
											   ['fechaDesde','<=',$fechaMov],
											   ['fechaHasta','>=',$fechaMov]])->first();
				$user3      = User::find($firmante->user_id);							   
				
				//fecha memo
				$date = new DateTime($movimiento->created_at);
				$m    = $date->format('m');
				switch ($m) {
					case '01': $mes = 'Enero'; break;
					case '02': $mes = 'Febrero'; break;
					case '03': $mes = 'Marzo'; break;
					case '04': $mes = 'Abril'; break;
					case '05': $mes = 'Mayo'; break;
					case '06': $mes = 'Junio'; break;
					case '07': $mes = 'Julio'; break;
					case '08': $mes = 'Agosto'; break;
					case '09': $mes = 'Septiembre'; break;
					case '10': $mes = 'Octubre'; break;
					case '11': $mes = 'Noviembre'; break;
					case '12': $mes = 'Diciembre'; break;
				}
				$fecha = $date->format('d').' de '.$mes.' de '.$date->format('Y');
				
				//fecha documento
				$date2  = new DateTime($documento->fechaEmision);
				$fecha2 = $date2->format('m').'/'. $date2->format('Y');
				
				//referente tecnico
				$movimiento2 = Movimiento::where([['documento_id',$id],['estado','RT']])->latest()->first();
				$user2       = User::find($movimiento2->user_id);
				
				//genera PDF
				$pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
					   ->loadView('factura.memoPdf',compact('documento','tipoDoc','proveedor','fecha','user','referente','fecha2','user2','user3'));
				return $pdf->stream();
			}
			catch(Exception $e){
				dd('Error al generar Memo Automático. Comuniquese con el administrador');
			}	
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Funcion que Muestra la Memo de Referente Tecnico Preeliminar en formato PDF
	 * Vista: factura.memoPdf
	 * Rol: None
     *
	 * @param  int  $id Identificador de Documentos
     * @return \Illuminate\Http\Response
     */
	public function memoPdfPre($id) 
	{
		if (Auth::check()) {
			try {
				$documento  = Documento::find($id);
				$tipoDoc    = TipoDoc::find($documento->tipoDoc_id);
				$proveedor  = Proveedor::find($documento->proveedor_id);
				$user       = null;
				$convenio   = Convenio::find($documento->convenio_id);
				$referente  = Referente::find($convenio->referente_id);
				
				$fecha = null;
				
				$fechaMov = new DateTime();
				$firmante   = Firmante::where([['establecimiento_id',$documento->establecimiento_id],
											   ['memo_id',1],
											   ['active',1],
											   ['fechaDesde','<=',$fechaMov],
											   ['fechaHasta','>=',$fechaMov]])->first();
				$user3      = User::find($firmante->user_id);							   
				
				//fecha documento
				$date2  = new DateTime($documento->fechaEmision);
				$fecha2 = $date2->format('m').'/'. $date2->format('Y');
				
				//referente tecnico
				$movimiento2 = Movimiento::where([['documento_id',$id],['estado','RT']])->latest()->first();
				$user2       = User::find($movimiento2->user_id);
				
				//genera PDF
				$pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
					   ->loadView('factura.memoPdf',compact('documento','tipoDoc','proveedor','fecha','user','referente','fecha2','user2','user3'));
				return $pdf->stream();
			}
			catch(Exception $e){
				dd('Error al generar Memo Automático. Comuniquese con el administrador');
			}	
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Funcion que Muestra la Memo de Convenios en formato PDF
	 * Vista: factura.memo2Pdf
	 * Rol: None
     *
	 * @param  int  $id Identificador de Documentos
     * @return \Illuminate\Http\Response
     */
	public function memo2Pdf($id) 
	{
		if (Auth::check()) {
			try {
				$documento  = Documento::find($id);
				$tipoDoc    = TipoDoc::find($documento->tipoDoc_id);
				$proveedor  = Proveedor::find($documento->proveedor_id);
				$movimiento = Movimiento::where([['documento_id',$id],['estado','DE']])->latest()->first();
				$convenio   = Convenio::find($documento->convenio_id);
				$referente  = Referente::find($convenio->referente_id);
				
				$validadors  = DB::table('validadors')
							  ->join('documento_validadors', 'documento_validadors.validador_id','=','validadors.id')
							  ->where('documento_validadors.documento_id',$id)
							  ->select('validadors.name as name')
							  ->orderBy('validadors.name')->get();	
				
				//determina firmante
				$fechaMov   = DateTime::createFromFormat('Y-m-d H:i:s', $movimiento->created_at);
				$fechaMov->setTime(0, 0);
				
				$firmante   = Firmante::where([['establecimiento_id',$documento->establecimiento_id],
											   ['memo_id',1],
											   ['active',1],
											   ['fechaDesde','<=',$fechaMov],
											   ['fechaHasta','>=',$fechaMov]])->first();
				$user1      = User::find($firmante->user_id);
				
				//determina receptor
				$firmante2  = Firmante::where([['establecimiento_id',$documento->establecimiento_id],
											   ['memo_id',2],
											   ['active',1],
											   ['fechaDesde','<=',$fechaMov],
											   ['fechaHasta','>=',$fechaMov]])->first();
				$user2      = User::find($firmante2->user_id);
				
				//determina revisor
				$revisor   = User::find($movimiento->user_id);
				
				//fecha memo
				$date = new DateTime($movimiento->created_at);
				$m    = $date->format('m');
				switch ($m) {
					case '01': $mes = 'Enero'; break;
					case '02': $mes = 'Febrero'; break;
					case '03': $mes = 'Marzo'; break;
					case '04': $mes = 'Abril'; break;
					case '05': $mes = 'Mayo'; break;
					case '06': $mes = 'Junio'; break;
					case '07': $mes = 'Julio'; break;
					case '08': $mes = 'Agosto'; break;
					case '09': $mes = 'Septiembre'; break;
					case '10': $mes = 'Octubre'; break;
					case '11': $mes = 'Noviembre'; break;
					case '12': $mes = 'Diciembre'; break;
				}
				$fecha = $date->format('d').' de '.$mes.' de '.$date->format('Y');
				
				//genera PDF
				$pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
					   ->loadView('factura.memo2Pdf',compact('documento','tipoDoc','proveedor','fecha','referente','validadors','user1','user2','revisor'));
				return $pdf->stream();
			}
			catch(Exception $e){
				dd('Error al generar Memo Automático. Comuniquese con el administrador');
			}	
		}
		else {
			return view('auth/login');
		}
	}
	
	/*******************************************************************************************/

	/*                                       AUTOCOMPLETAR                                     */
	/*******************************************************************************************/
	/**
	 * Funcion Autocompleta con campo de proveedores con RUT
	 * Rol: None
	 *
     * @param  \Illuminate\Http\Request  $request	 
	 * @return list lista de proveedores que coinciden con la busqueda
	 */
	public function autoComplete(Request $request) 
	{
        $query = $request->get('term','');
        
        $proveedors=Proveedor::where([['rut','LIKE','%'.$query.'%'],['active','1']])->orWhere([[DB::raw('LOWER(name)'),'LIKE','%'.strtolower($query).'%'],['active','1']])->orderBy('name')->get();
        
        $data=array();
        foreach ($proveedors as $proveedor) {
				$data[]=array('value'=>$proveedor->rut."-".$proveedor->dv." ".$proveedor->name);
        }
        if(count($data))
             return $data;
        else
            return ['value'=>'Proveedor no encontrado'];
	}
	
	/*******************************************************************************************/
	/*                                       FUNCIONES		                                   */
	/*******************************************************************************************/
	/**
	 * Funcion que Valida si el valor ingresado es numerico
	 * Rol: None
	 *
     * @param  string $texto 
	 * @return boolean true si es numerico, sino falso
	 */
	public function IsNumeric($texto)
	{
		$permitidos = "0123456789"; 
		
		for ($i=0; $i<strlen($texto); $i++){ 
			if (strpos($permitidos, substr($texto,$i,1))===false){ 
				return false; 
			} 
		} 
		return true; 
	}

}

