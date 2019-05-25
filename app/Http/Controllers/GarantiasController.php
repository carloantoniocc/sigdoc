<?php

namespace sigdoc\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use sigdoc\Garantia;
use sigdoc\Proveedor;
use sigdoc\TipoDoc;
use sigdoc\Banco;
use sigdoc\Moneda;
use sigdoc\ObjetoGarantia;
use sigdoc\MovimientoGarantia;
use sigdoc\Firmante;
use sigdoc\User;
use sigdoc\OracleGarantia;

use DateTime;
use PDF;
use Excel;

use DB;
use Illuminate\Support\Facades\Auth;

use Exception;

/**
 * Clase Controlador Documentos de Garantías
 * Rol: Por Funcion
 */
class GarantiasController extends Controller
{
    /*******************************************************************************************/
	/*                                   OFICINA DE PARTES                                     */
	/*******************************************************************************************/
	/**
     * Función que Crea Documento de Garantia en Oficina de Partes
	 * Vista: garantia.oficinaPartes.create
	 * Rol: oficinaPartes
     *
     * @return \Illuminate\Http\Response
     */
	public function create()
    {
		if (Auth::check()) { 
			$tipos   = TipoDoc::where([['active',1],['flujo',2]])->orderBy('name')->get();
			$bancos  = Banco::where('active',1)->orderBy('name')->get();
			$monedas = Moneda::where('active',1)->orderBy('name')->get();
			$objetos = ObjetoGarantia::where('active',1)->orderBy('name')->get();

			return view('garantia.oficinaPartes.create',compact('tipos','bancos','monedas','objetos'));		
		}
		else {
			return view('auth/login');
		}
    }
	
	/**
     * Función que Almacena documento creado por oficina de partes.
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
				'nDoc' => 'required|string|max:20',
				'tipo' => 'required',
				'fechaRecepcion' => 'required',
				'fechaEmision' => 'required',
				'fechaVencimiento' => 'required',
				'monto' => 'required|min:0|regex:/^\d*(\.\d{1,4})?$/',
				'licitacion' => 'required|string|max:150',
				'observacion' => 'nullable|string|max:150',
				"archivo" => "nullable|mimes:pdf|max:10024",
			]);
			
			if ($validator->fails()) {
				return redirect('garantias/oficinaPartes')
							->withErrors($validator)
							->withInput();
			}
			else {
				//establecimiento usuario
				//$estab_user = $request->session()->get('establecimiento');
				$user       = User::find(Auth::user()->id);
				$estab_user = $user->establecimientos()->first()->id;
			
				//determina el ID del proveedor
				$explode = explode("-",$request->proveedor);
				$rut = $explode[0];
				$proveedor = Proveedor::where('rut',$rut)->first();
				
				//si proveedor no existe
				if ($proveedor == null) {
					return redirect('/garantias/oficinaPartes')->with('message','proveedor')->withInput();
				}
				
				//adjunta archivo
				$archivoName = null;
				
				if ($request->hasFile('archivo')) {
					$archivo = $request->file('archivo');
					$archivoName = 'g'.time().$archivo->getClientOriginalName();
					
					//guarda archivo
					$request->file('archivo')->storeAs('public',$archivoName);
				}
				
				//formatea fechas
				$fechaEmision = DateTime::createFromFormat('d-m-Y', $request->fechaEmision);
				$fechaRecepcion = DateTime::createFromFormat('d-m-Y', $request->fechaRecepcion);
				$fechaVencimiento = DateTime::createFromFormat('d-m-Y', $request->fechaVencimiento);
				
				//guarda datos de garantia
				$garantia = new Garantia;
				
				$garantia->proveedor_id       = $proveedor->id;
				$garantia->tipoDoc_id         = $request->tipo;
				$garantia->banco_id           = $request->banco;
				$garantia->nDoc               = $request->nDoc;
				$garantia->moneda_id          = $request->moneda;
				$garantia->monto              = $request->monto;
				$garantia->objeto_garantia_id = $request->objeto;
				$garantia->licitacion         = $request->licitacion;
				$garantia->fechaEmision       = $fechaEmision;
				$garantia->fechaRecepcion     = $fechaRecepcion;
				$garantia->fechaVencimiento   = $fechaVencimiento;
				if($archivoName != null) {
					$garantia->archivo = Storage::url($archivoName);
				}
				$garantia->user_id            = Auth::user()->id;
				$garantia->establecimiento_id = $estab_user;
				
				$garantia->save();
				
				//guarda movimiento
				if ( $garantia->id != null ) {
					$movimiento = new MovimientoGarantia;
					
					$movimiento->garantia_id = $garantia->id;
					$movimiento->fechaVencimiento = $fechaVencimiento;
					$movimiento->estado = 'OP';
					$movimiento->observacion = str_replace(PHP_EOL,"<br>",$request->observacion);
					$movimiento->user_id = Auth::user()->id;
					
					$movimiento->save();
				
				
					//almacena datos en ABEX
					$proveedor = Proveedor::find($garantia->proveedor_id);
					$tipo      = TipoDoc::find($garantia->tipoDoc_id);
					$banco     = Banco::find($garantia->banco_id);
					$objetos   = ObjetoGarantia::find($garantia->objeto_garantia_id);
					
					try {
						$oracle = new OracleGarantia;
						
						$oracle->proveedor_rut     = $proveedor->rut."-".$proveedor->dv;
						$oracle->proveedor_nombre  = $proveedor->name;
						$oracle->tipo_doc_id       = $garantia->tipoDoc_id;
						$oracle->banco_id          = $garantia->banco_id;
						$oracle->num_doc           = $garantia->nDoc;
						$oracle->moneda_id         = $garantia->moneda_id;
						$oracle->monto             = $garantia->monto;
						$oracle->tipo_garantia_id  = $garantia->objeto_garantia_id;
						$oracle->licitacion        = $garantia->licitacion;
						$oracle->fecha_vencimiento = $garantia->fechaVencimiento;
						$oracle->estado            = 'OP';
						$oracle->fecha_creado      = $garantia->created_at; 
						$oracle->fecha_modificado  = $garantia->updated_at;
						$oracle->tipo_doc_nombre   = $tipo->name;
						$oracle->banco_nombre      = $banco->name;
						$oracle->tipo_garantia_nombre = $objetos->name;
						$oracle->estado_nombre        = 'Oficina de Partes';
						$oracle->id                   = $garantia->id; 
						
						$oracle->save();
					}
					catch(Exception $e) {}
					//fin almacena datos en ABEX
				}
				
				return redirect('/garantias/oficinaPartes')->with('message','store');
			}	
			
		}
		else {
			return view('auth/login');
		}
    }
	
	/**
     * Función que Lista los documentos de garantia que fueron ingresados en Oficina de Partes en estado OP.
	 * Vista: garantia.oficinaPartes.nomina
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
			
			$garantias =  DB::table('garantias')
							->join('movimiento_garantias', 'garantias.id','=','movimiento_garantias.garantia_id')
							->join('proveedors','garantias.proveedor_id','=','proveedors.id')
							->join('tipo_docs','garantias.tipoDoc_id','=','tipo_docs.id')
							->join('bancos','garantias.banco_id','=','bancos.id')
							->join('monedas','garantias.moneda_id','=','monedas.id')
							->join('objeto_garantias','garantias.objeto_garantia_id','=','objeto_garantias.id')
							->select('proveedors.rut as rut',
									 'proveedors.name as nameProveedor',
									 'tipo_docs.name as tipoDoc',
									 'monedas.name as moneda',
									 'garantias.monto as monto',
									 'garantias.id as id',
									 'garantias.nDoc as nDoc',
									 'garantias.archivo as archivo',
									 'garantias.nomina as nomina')
							->selectRaw('DATE_FORMAT(garantias.fechaRecepcion, "%d-%m-%Y") as fechaRecepcion')
							->where([
									['garantias.establecimiento_id','=',$estab_user],
									['movimiento_garantias.active',1],
									['movimiento_garantias.estado','OP'],
									['rut', 'LIKE', '%'.$request->get('searchRut').'%'],
									['tipo_docs.id', 'LIKE', '%'.$request->get('searchTipo').'%'],
									['nDoc', 'LIKE', '%'.$request->get('searchNdoc').'%'],
									])
							->orderBy('garantias.id')->paginate(500)
							->appends('searchRut',$request->get('searchRut'))
							->appends('searchTipo',$request->get('searchTipo'))
							->appends('searchNdoc',$request->get('searchNdoc'));
			
			$tipos = TipoDoc::where([['active',1],['flujo',2]])->orderBy('name')->get(); 
			
			return view('garantia.oficinaPartes.nomina',compact('garantias','tipos'));
			
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Función que Genera Número de Nómina para documentos seleccionados.
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
					$garantia = Garantia::find($check);
					
					if ($garantia->archivo != null) { //asigna nomina solo a documentos con archivo adjunto
						$garantia->nomina = $nomina; 
						$garantia->save();
					}
					else {
						$aux = $aux + 1;
					}
				}
			}
			if ($aux == 0) {
				return redirect('garantias/oficinaPartes/nomina')->with('message','nomina');
			}
			else {
				return redirect('garantias/oficinaPartes/nomina')->with('message','nominaWarning');
			}
		}
		else {
			return view('auth/login');
		}	
	}
	
	/**
     * Función que Envia documentos a Convenios o Abastecimiento.
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
			
			$garantias = DB::table('garantias')
								->join('movimiento_garantias', 'garantias.id','=','movimiento_garantias.garantia_id')
								->where([
										['garantias.establecimiento_id','=',$estab_user],
										['movimiento_garantias.active',1],
										['movimiento_garantias.estado','OP']
										])
								->whereNotNull('garantias.nomina')
								->select('garantias.id as id', 'garantias.fechaVencimiento as fechaVencimiento', 'movimiento_garantias.id as movimiento_id')->get();
			
			foreach($garantias as $garantia) {
				//cambia estado de movimiento de activo a no activo
				$id = $garantia->movimiento_id; 
				$movimiento = MovimientoGarantia::find($id); 
				$movimiento->active = 0;
				
				$movimiento->save();

				//guarda nuevo movimiento
				$movimientoNew = new MovimientoGarantia;
				
				$movimientoNew->garantia_id      = $garantia->id;
				$movimientoNew->estado           = 'NP';
				$movimientoNew->observacion      = $movimiento->observacion;
				$movimientoNew->fechaVencimiento = $garantia->fechaVencimiento;
				$movimientoNew->user_id          = Auth::user()->id;
					
				$movimientoNew->save();
				
				//almacena datos en ABEX
				try {
					$result = DB::connection('oracle')->update("UPDATE GARANTIAS SET ESTADO = 'NP', ESTADO_NOMBRE = 'Gestion de Garantia', FECHA_MODIFICADO = '".$movimientoNew->created_at."' WHERE ID = ".$movimientoNew->garantia_id);	
				}
				catch(Exception $e) {}
				//fin datos en ABEX
			}
			
			return redirect('garantias/oficinaPartes/nomina')->with('message','envioNomina');
		}	
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Función que lista las nominas generadas.
	 * Vista: garantia.oficinaPartes.listaNomina
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
			
			
			$garantias  = DB::table('garantias')
			              ->select('nomina')
						  ->where('garantias.establecimiento_id',$estab_user)
						  ->whereRaw('LENGTH(garantias.nomina) < 12 and LENGTH(garantias.nomina) > 9') /*muestra solo nominas de nueva version*/
						  ->whereNotNull('nomina')
						  ->groupBy('nomina')
						  ->orderBy('nomina', 'desc')->paginate(10);
			
			return view('garantia.oficinaPartes.listaNomina',compact('garantias'));
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Función que genera impresión de nóminas.
	 * Vista: garantia.oficinaPartes.printNomina
	 * Rol: oficinaPartes
     *
     * @param  \Illuminate\Http\Request  $request
	 * @param int nomina Número de Nomina a Imprimir
	 * @return \Illuminate\Http\Response
     */
	public function printNomina(Request $request,$nomina) 
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			
			$garantias = DB::table('garantias')
							->join('proveedors','garantias.proveedor_id','=','proveedors.id')
							->join('tipo_docs','garantias.tipoDoc_id','=','tipo_docs.id')
							->select('proveedors.rut as rut',
									 'proveedors.name as nameProveedor',
									 'tipo_docs.name as tipoDoc',
									 'garantias.id as id',
									 'garantias.nDoc as nDoc',
									 'garantias.monto as monto',
									 'garantias.nomina as nomina')
							->selectRaw('DATE_FORMAT(garantias.fechaRecepcion, "%d-%m-%Y") as fechaRecepcion')
							->selectRaw('DATE_FORMAT(garantias.fechaEmision, "%d-%m-%Y") as fechaEmision')
							->where([['nomina',$nomina],
									 ['garantias.establecimiento_id','=',$estab_user]])
							->orderBy('garantias.id')->get();
			
			
			return view('garantia.oficinaPartes.printNomina',compact('garantias'));
		}
		else {
			return view('auth/login');
		}
	}
	/*******************************************************************************************/
	/*                               ABASTECIMIENTO - CONVENIOS                                */
	/*******************************************************************************************/
	/**
     * Funcion que lista Documentos de Convenios
	 * Vista: garantia.convenios.index
	 * Rol: convenios
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function convenio(Request $request)
    {	
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			
			$garantias = DB::table('garantias')
							->join('proveedors','garantias.proveedor_id','=','proveedors.id')
							->join('tipo_docs','garantias.tipoDoc_id','=','tipo_docs.id')
							->join('bancos','garantias.banco_id','=','bancos.id')
							->join('monedas','garantias.moneda_id','=','monedas.id')
							->join('objeto_garantias','garantias.objeto_garantia_id','=','objeto_garantias.id')
							->join('movimiento_garantias', 'garantias.id','=','movimiento_garantias.garantia_id')
							->select('proveedors.rut as rut',
									 'proveedors.name as nameProveedor',
									 'tipo_docs.name as tipoDoc',
									 'bancos.name as banco',
									 'monedas.name as moneda',
									 'objeto_garantias.name as objeto_garantia',
									 'garantias.id as id',
									 'garantias.nDoc as nDoc',
									 'garantias.monto as monto',
									 'garantias.nomina as nomina',
									 'garantias.licitacion as licitacion',
									 'garantias.archivo as archivo',
									 'movimiento_garantias.estado as estado',
									 'movimiento_garantias.observacion as observacion')
							->selectRaw('DATE_FORMAT(garantias.fechaRecepcion, "%d-%m-%Y") as fechaRecepcion')
							->selectRaw('DATE_FORMAT(garantias.fechaEmision, "%d-%m-%Y") as fechaEmision')
							->selectRaw('DATE_FORMAT(movimiento_garantias.fechaVencimiento, "%d-%m-%Y") as fechaVencimiento')
							->where([['objeto_garantias.flujo','1'], /**/
									 ['garantias.establecimiento_id','=',$estab_user],
									 ['movimiento_garantias.active',1],
									 ['rut', 'LIKE', '%'.$request->get('searchRut').'%'],
									 ['tipo_docs.id', 'LIKE', '%'.$request->get('searchTipo').'%'],
									 ['nDoc', 'LIKE', '%'.$request->get('searchNdoc').'%']])
							->whereIn('movimiento_garantias.estado',['NP','RN'])		
							->orderBy('garantias.id','desc')
							->paginate(10)
							->appends('searchRut',$request->get('searchRut'))
							->appends('searchTipo',$request->get('searchTipo'))
							->appends('searchNdoc',$request->get('searchNdoc'));
			
			$tipos = TipoDoc::where([['active',1],['flujo',2]])->orderBy('name')->get(); 
			
			return view('garantia.convenios.index',compact('garantias','tipos'));
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Funcion que lista Documentos de Abastecimiento
	 * Vista: garantia.abastecimiento.index
	 * Rol: abastecimiento
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function abastecimiento(Request $request)
    {	
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			
			$garantias = DB::table('garantias')
							->join('proveedors','garantias.proveedor_id','=','proveedors.id')
							->join('tipo_docs','garantias.tipoDoc_id','=','tipo_docs.id')
							->join('bancos','garantias.banco_id','=','bancos.id')
							->join('monedas','garantias.moneda_id','=','monedas.id')
							->join('objeto_garantias','garantias.objeto_garantia_id','=','objeto_garantias.id')
							->join('movimiento_garantias', 'garantias.id','=','movimiento_garantias.garantia_id')
							->select('proveedors.rut as rut',
									 'proveedors.name as nameProveedor',
									 'tipo_docs.name as tipoDoc',
									 'bancos.name as banco',
									 'monedas.name as moneda',
									 'objeto_garantias.name as objeto_garantia',
									 'garantias.id as id',
									 'garantias.nDoc as nDoc',
									 'garantias.monto as monto',
									 'garantias.nomina as nomina',
									 'garantias.licitacion as licitacion',
									 'garantias.archivo as archivo',
									 'movimiento_garantias.estado as estado',
									 'movimiento_garantias.observacion as observacion')
							->selectRaw('DATE_FORMAT(garantias.fechaRecepcion, "%d-%m-%Y") as fechaRecepcion')
							->selectRaw('DATE_FORMAT(garantias.fechaEmision, "%d-%m-%Y") as fechaEmision')
							->selectRaw('DATE_FORMAT(movimiento_garantias.fechaVencimiento, "%d-%m-%Y") as fechaVencimiento')
							->where([['objeto_garantias.flujo','2'], /**/
									 ['garantias.establecimiento_id','=',$estab_user],
									 ['movimiento_garantias.active',1],
									 ['rut', 'LIKE', '%'.$request->get('searchRut').'%'],
									 ['tipo_docs.id', 'LIKE', '%'.$request->get('searchTipo').'%'],
									 ['nDoc', 'LIKE', '%'.$request->get('searchNdoc').'%']])
							->whereIn('movimiento_garantias.estado',['NP','RN'])		
							->orderBy('garantias.id','desc')
							->paginate(10)
							->appends('searchRut',$request->get('searchRut'))
							->appends('searchTipo',$request->get('searchTipo'))
							->appends('searchNdoc',$request->get('searchNdoc'));
			
			$tipos = TipoDoc::where([['active',1],['flujo',2]])->orderBy('name')->get(); 
			
			return view('garantia.abastecimiento.index',compact('garantias','tipos'));
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Función que Llama al formulario de envio de documentos a tesorería
	 * Vista: garantia.enviar
	 * Rol: abastecimiento - convenio
     *
     * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @param  int  $flujo flujo que renueva documento (2 - Convenios; 3 - Abastecimiento)
     * @return \Illuminate\Http\Response
     */
    public function enviarTesoreria(Request $request, $id, $flujo)
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			
			$garantia = Garantia::find($id);
			
			$mov = MovimientoGarantia::where([['garantia_id',$id],['active',1]])
				   ->select('estado as estado')
				   ->selectRaw('DATE_FORMAT(fechaVencimiento, "%d-%m-%Y") as fechaVencimiento')
				   ->first(); 

			if ( Auth::user()->isRole('Convenios') && $flujo == 2 && ($mov->estado != 'NP' && $mov->estado != 'RN') ) { //documento no disponible para renovar por convenios
				return view('home');
			}
			
			if ( Auth::user()->isRole('Abastecimiento') && $flujo == 3 && ($mov->estado != 'NP' && $mov->estado != 'RN') ) { //documento no disponible para renovar por abastecimiento
				return view('home');
			}
			
			if ( $garantia->establecimiento_id == $estab_user ) {
				
				$proveedor = Proveedor::find($garantia->proveedor_id);
				$tipo      = TipoDoc::find($garantia->tipoDoc_id);
				$banco     = Banco::find($garantia->banco_id);
				
				return view('garantia.enviar',compact('garantia','proveedor','tipo','banco','flujo','mov'));
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
     * Función que Renueva documentos
	 * Rol: abastecimiento - convenio
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function movEnviar(Request $request) 
	{
		if (Auth::check()) {
			// validate requeridos
			$validator = validator::make($request->all(), [
				"archivo" => "nullable|mimes:pdf|max:10024",
			]);
			
			if ($validator->fails()) {
				if ($request->flujo == 2) { //secretaria de convenios
					return redirect('garantias/convenios/'.$request->garantia_id.'/enviar/2')->withErrors($validator)->withInput();
				}
				elseif ($request->flujo == 3) { //convenios
					return redirect('garantias/abastecimiento/'.$request->garantia_id.'/enviar/3')->withErrors($validator)->withInput();
				}
			}
			else {
				
				//adjunta archivo
				$archivoName = null;
				
				if ($request->hasFile('archivo')) {
					$archivo = $request->file('archivo');
					$archivoName = 'c'.time().$archivo->getClientOriginalName();
					
					//guarda archivo
					$request->file('archivo')->storeAs('public',$archivoName);
				}
				
				//guarda datos en garantia
				$garantia = Garantia::find($request->garantia_id);
				
				if($archivoName != null) {
					$garantia->archivoConformidad = Storage::url($archivoName);
				}
				$garantia->estado = $request->estado;
				
				$garantia->save();
				
				//cambia estado de movimiento de activo a no activo
				$mov = MovimientoGarantia::where([['garantia_id',$request->garantia_id],['active',1]])->first();
				
				$id = $mov->id; 
				$movimiento = MovimientoGarantia::find($id);
				$movimiento->active = 0;
				
				$movimiento->save();
				
				//guarda nuevo movimiento
				$movimientoNew = new MovimientoGarantia;
				
				$movimientoNew->garantia_id      = $request->garantia_id;
				$movimientoNew->estado           = 'TE';
				$movimientoNew->observacion      = str_replace(PHP_EOL,"<br>",$request->observacion);
				$movimientoNew->fechaVencimiento = $movimiento->fechaVencimiento;
				$movimientoNew->user_id          = Auth::user()->id;
					
				$movimientoNew->save();
				
				//almacena datos en ABEX
				try {
					$result = DB::connection('oracle')->update("UPDATE GARANTIAS SET ESTADO = 'TE', ESTADO_NOMBRE = 'En Tesoreria', FECHA_MODIFICADO = '".$movimientoNew->created_at."' WHERE ID = ".$movimientoNew->garantia_id);	
				}
				catch(Exception $e) {}
				//fin datos en ABEX
				
				if ($request->flujo == 2) { //secretaria de convenios
					return redirect('garantias/convenios')->with('message','envio');
				}
				elseif ($request->flujo == 3) { //convenios
					return redirect('garantias/abastecimiento')->with('message','envio');
				}
			}	
		}
		else {
			return view('auth/login');
		}	
	}
	
	/*******************************************************************************************/
	/*                                        TESORERIA                                        */
	/*******************************************************************************************/
	/**
     * Función que Lista de Documentos en Tesoreria
	 * Vista: 
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
			
			
			$garantias = DB::table('garantias')
							->join('proveedors','garantias.proveedor_id','=','proveedors.id')
							->join('tipo_docs','garantias.tipoDoc_id','=','tipo_docs.id')
							->join('bancos','garantias.banco_id','=','bancos.id')
							->join('monedas','garantias.moneda_id','=','monedas.id')
							->join('objeto_garantias','garantias.objeto_garantia_id','=','objeto_garantias.id')
							->join('movimiento_garantias', 'garantias.id','=','movimiento_garantias.garantia_id')
							->select('proveedors.rut as rut',
									 'proveedors.name as nameProveedor',
									 'tipo_docs.name as tipoDoc',
									 'bancos.name as banco',
									 'monedas.name as moneda',
									 'objeto_garantias.name as objeto_garantia',
									 'garantias.id as id',
									 'garantias.nDoc as nDoc',
									 'garantias.monto as monto',
									 'garantias.nomina as nomina',
									 'garantias.licitacion as licitacion',
									 'garantias.archivo as archivo',
									 'garantias.estado as estado',
									 'movimiento_garantias.observacion as observacion')
							->selectRaw('DATE_FORMAT(garantias.fechaRecepcion, "%d-%m-%Y") as fechaRecepcion')
							->selectRaw('DATE_FORMAT(garantias.fechaEmision, "%d-%m-%Y") as fechaEmision')
							->selectRaw('DATE_FORMAT(movimiento_garantias.fechaVencimiento, "%d-%m-%Y") as fechaVencimiento')
							->where([['garantias.establecimiento_id','=',$estab_user],
									 ['movimiento_garantias.active',1],
									 ['rut', 'LIKE', '%'.$request->get('searchRut').'%'],
									 ['tipo_docs.id', 'LIKE', '%'.$request->get('searchTipo').'%'],
									 ['nDoc', 'LIKE', '%'.$request->get('searchNdoc').'%']])
							->whereIn('movimiento_garantias.estado',['TE'])		
							->orderBy('garantias.id')
							->paginate(10)
							->appends('searchRut',$request->get('searchRut'))
							->appends('searchTipo',$request->get('searchTipo'))
							->appends('searchNdoc',$request->get('searchNdoc'));
			
			$tipos = TipoDoc::where([['active',1],['flujo',2]])->orderBy('name')->get(); 
			
			return view('garantia.tesoreria.index',compact('garantias','tipos'));
		}	
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Función que llama Formulario de Ingreso datos de cobro de boleta de garantía
	 * Vista: garantia.tesoreria.cobrar
	 * Rol: tesoreria
     *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
     * @return \Illuminate\Http\Response
     */	
	public function cobrar(Request $request, $id)
    {
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			
			$garantia = Garantia::find($id);
			
			$mov = MovimientoGarantia::where([['garantia_id',$id],['active',1]])->first(); 
			
			if ( Auth::user()->isRole('Tesoreria') && $mov->estado != 'TE') { //documento no disponible si no está en tesoreria
				return view('home');
			}
			
			if ( $garantia->establecimiento_id == $estab_user ) {
				
				$proveedor = Proveedor::find($garantia->proveedor_id);
				$tipo      = TipoDoc::find($garantia->tipoDoc_id);
				$banco     = Banco::find($garantia->banco_id);
				
				return view('garantia.tesoreria.cobrar',compact('garantia','proveedor','tipo','banco','mov'));
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
     * Función que llama Formulario de Ingreso datos de devolucion de boleta de garantía
	 * Vista: garantia.tesoreria.devolver
	 * Rol: tesoreria
     *
	 * @param  \Illuminate\Http\Request  $request	 
	 * @param  int  $id
     * @return \Illuminate\Http\Response
     */	
	public function devolver(Request $request, $id)
    {
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			
			$garantia = Garantia::find($id);
			
			$mov = MovimientoGarantia::where([['garantia_id',$id],['active',1]])->first(); 
			
			if ( Auth::user()->isRole('Tesoreria') && $mov->estado != 'TE') { //documento no disponible si no está en tesoreria
				return view('home');
			}
			
			if ( $garantia->establecimiento_id == $estab_user ) {
				
				$proveedor = Proveedor::find($garantia->proveedor_id);
				$tipo      = TipoDoc::find($garantia->tipoDoc_id);
				$banco     = Banco::find($garantia->banco_id);
				
				return view('garantia.tesoreria.devolver',compact('garantia','proveedor','tipo','banco','mov'));
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
	 * Función que almacena movimiento de Devolución o Cobro de Documento
	 * Rol: tesoreria
	 *
	 * @param \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function movimientoCobrarDevolver(Request $request){
		if (Auth::check()) {
			//guarda datos en garantia
			$garantia = Garantia::find($request->garantia_id);
				
			$fecha = DateTime::createFromFormat('d-m-Y', $request->fecha);
			
			$garantia->tesoreria_fecha  = $fecha;
			$garantia->tesoreria_rut    = $request->rut;
			$garantia->tesoreria_nombre = $request->nombre;
			
			$garantia->save();
			
			//determina estado segun cobro o devolucion
			if( $garantia->estado == 1 ){ //cobro
				$estado = 'CG';
			}
			else { //devolucion
				$estado = 'DG';
			}
			
			//cambia estado de movimiento de activo a no activo
			$mov = MovimientoGarantia::where([['garantia_id',$request->garantia_id],['active',1]])->first();
			
			$id = $mov->id; 
			$movimiento = MovimientoGarantia::find($id);
			$movimiento->active = 0;
			
			$movimiento->save();
			
			//guarda nuevo movimiento
			$movimientoNew = new MovimientoGarantia;
			
			$movimientoNew->garantia_id      = $request->garantia_id;
			$movimientoNew->estado           = $estado;
			$movimientoNew->fechaVencimiento = $movimiento->fechaVencimiento;
			$movimientoNew->user_id          = Auth::user()->id;
				
			$movimientoNew->save();
			
			if ($garantia->estado == 1) { //cobro
				//almacena datos en ABEX
				try {
					$result = DB::connection('oracle')->update("UPDATE GARANTIAS SET ESTADO = 'CG', ESTADO_NOMBRE = 'Cobro', FECHA_MODIFICADO = '".$movimientoNew->created_at."' WHERE ID = ".$movimientoNew->garantia_id);	
				}
				catch(Exception $e) {}
				//fin datos en ABEX
			}
			else { //devolucion
				//almacena datos en ABEX
				try {
					$result = DB::connection('oracle')->update("UPDATE GARANTIAS SET ESTADO = 'DG', ESTADO_NOMBRE = 'Devolucion', FECHA_MODIFICADO = '".$movimientoNew->created_at."' WHERE ID = ".$movimientoNew->garantia_id);	
				}
				catch(Exception $e) {}
				//fin datos en ABEX
			}
			
			
			if ($garantia->estado == 1) { //cobro
				return redirect('garantias/tesoreria')->with('message','cobro');
			}
			else { //devolucion
				return redirect('garantias/tesoreria')->with('message','devuelto');
			}
		}
		else {
			return view('auth/login');
		}	
	}	
	
	
	/*******************************************************************************************/
	/*                                   MODULOS GENERICOS                                     */
	/*******************************************************************************************/
	/**
     * Función para la Edición de Documentos.
	 * Vista: garantia.edit
	 * Rol: abastecimiento - convenio - oficinaPartes
     *
	 * @param \Illuminate\Http\Request  $request
     * @param  int  $id
	 * @param  int  $flujo Flujo que edita garantia (1 - oficina de Partes ; 2 - Convenios; 3 - Abastecimiento)
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id, $flujo)
    {
        if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			
			$garantia = Garantia::find($id);
			
			//determina si la garantia esta en oficina de parte (flujo = 1) o convenios (flujo = 2) o abastecimiento (flujo = 3)
			$aux = 0; 
			if ( Auth::user()->isRole('Oficina de Partes') && $flujo == 1 ) {
				$aux = MovimientoGarantia::where([['garantia_id',$garantia->id],['estado','OP'],['active','1']])->count();
			}
			elseif ( Auth::user()->isRole('Convenios') && $flujo == 2 ) {
				$aux = MovimientoGarantia::where([['garantia_id',$garantia->id],['estado','NP'],['active','1']])->count();
			}
			elseif ( Auth::user()->isRole('Abastecimiento') && $flujo == 3 ) {
				$aux = MovimientoGarantia::where([['garantia_id',$garantia->id],['estado','NP'],['active','1']])->count();
			}
			
			if ( $garantia->establecimiento_id == $estab_user && $aux == 1 ) {
				
				$proveedor = Proveedor::find($garantia->proveedor_id);
				
				$tipos = TipoDoc::where([['active',1],['flujo',2]])->orderBy('name')->get();
				
				$bancos  = Banco::where('active',1)->orderBy('name')->get();
				
				$monedas = Moneda::where('active',1)->orderBy('name')->get();
				
				$objetos = ObjetoGarantia::where('active',1)->orderBy('name')->get();
				
				return view('garantia.edit',compact('garantia','proveedor','tipos','flujo','bancos','monedas','objetos'));
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
     * Función para la Actualización de Documentos
	 * Rol: abastecimiento - convenio - oficinaParte
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
		if (Auth::check()) {
			// validate requeridos
			$validator = validator::make($request->all(), [
				'nDoc' => 'required|string|max:20',
				'tipo' => 'required',
				'fechaRecepcion' => 'required',
				'fechaEmision' => 'required',
				'fechaVencimiento' => 'required',
				'monto' => 'required|min:0|regex:/^\d*(\.\d{1,4})?$/',
				'licitacion' => 'required|string|max:150',
				'observacion' => 'nullable|string|max:150',
				"archivo" => "nullable|mimes:pdf|max:10024",
			]);
			
			//determina formulario segun flujo
			if ( $request->flujo == 1 ) { //oficina de partes
				$modulo	= 'garantias/oficinaPartes/';
			}
			elseif ( $request->flujo == 2 ) { //convenios
				$modulo	= 'garantias/convenios/';
			}
			elseif ( $request->flujo == 3 ) { //abastecimiento
				$modulo	= 'garantias/abastecimiento/';
			}
			
			if ($validator->fails()) {
				return redirect($modulo.$id.'/edit/'.$request->flujo)
							->withErrors($validator)
							->withInput();
			}
			else {
				//adjunta archivo
				$archivoName = null;
				
				if ($request->hasFile('archivo')) {
					$archivo = $request->file('archivo');
					$archivoName = 'g'.time().$archivo->getClientOriginalName();
					
					//guarda archivo
					$request->file('archivo')->storeAs('public',$archivoName);
				}

				//formatea fechas
				$fechaEmision = DateTime::createFromFormat('d-m-Y', $request->fechaEmision);
				$fechaRecepcion = DateTime::createFromFormat('d-m-Y', $request->fechaRecepcion);
				$fechaVencimiento = DateTime::createFromFormat('d-m-Y', $request->fechaVencimiento);
				
				//guarda datos de garantia
				$garantia = Garantia::find($id);
				
				$garantia->tipoDoc_id         = $request->tipo;
				$garantia->banco_id           = $request->banco;
				$garantia->nDoc               = $request->nDoc;
				$garantia->moneda_id          = $request->moneda;
				$garantia->monto              = $request->monto;
				$garantia->objeto_garantia_id = $request->objeto;
				$garantia->licitacion         = $request->licitacion;
				$garantia->fechaEmision       = $fechaEmision;
				$garantia->fechaRecepcion     = $fechaRecepcion;
				$garantia->fechaVencimiento   = $fechaVencimiento;
				if($archivoName != null) {
					$garantia->archivo = Storage::url($archivoName);
				}
				$garantia->user_id            = Auth::user()->id;
				
				$garantia->save();
				
				//modifica fecha de vencimiento
				$movimientos = MovimientoGarantia::where('garantia_id',$id)->get();
				
				foreach($movimientos as $movimiento) {
					$mov = MovimientoGarantia::find($movimiento->id);
					$mov->fechaVencimiento = $fechaVencimiento;
					
					$mov->save();
				}
				
				if ($request->flujo == 1) { //edicion solicitada por oficina de partes
					return redirect('garantias/oficinaPartes/nomina')->with('message','update');
				}
				elseif ($request->flujo == 2) {
					return redirect('garantias/convenios')->with('message','update');
				}
				elseif ($request->flujo == 3) {
					return redirect('garantias/abastecimiento')->with('message','update');
				}
				
			}
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Función que Llama al formulario de rechazo
	 * Vista: garantia.rechazar
	 * Rol: abastecimiento - convenio
     *
     * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @param  int  $flujo Flujo que edita documento (1 - oficina de Partes ; 2 - Convenios; 3 - Abastecimiento)
     * @return \Illuminate\Http\Response
     */
    public function rechazar(Request $request, $id, $flujo)
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			
			$garantia = Garantia::find($id);
			
			$mov = MovimientoGarantia::where([['garantia_id',$id],['active',1]])->first(); 
			
			if ( Auth::user()->isRole('Oficina de Partes') && $flujo == 1 && $mov->estado != 'OP') { //documento no disponible para rechazar por oficina de partes
				return view('home');
			}
			
			if ( Auth::user()->isRole('Convenios') && $flujo == 2 && $mov->estado != 'NP') { //documento no disponible para rechazar por convenios
				return view('home');
			}
			
			if ( Auth::user()->isRole('Abastecimiento') && $flujo == 3 && $mov->estado != 'NP') { //documento no disponible para rechazar por convenios
				return view('home');
			}
			
			if ( $garantia->establecimiento_id == $estab_user ) {
				
				$proveedor = Proveedor::find($garantia->proveedor_id);
				$tipo      = TipoDoc::find($garantia->tipoDoc_id);
				$banco     = Banco::find($garantia->banco_id);
				
				return view('garantia.rechazar',compact('garantia','proveedor','tipo','banco','flujo'));
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
     * Función para el Rechazo de documentos
	 * Rol: abastecimiento - convenio
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function movRechazar(Request $request) 
	{
		if (Auth::check()) {
			
			$mov = MovimientoGarantia::where([['garantia_id',$request->garantia_id],['active',1]])->first();
			
			//cambia estado de movimiento de activo a no activo
			$id = $mov->id; 
			$movimiento = MovimientoGarantia::find($id);
			$movimiento->active = 0;
			
			$movimiento->save();
			
			//guarda nuevo movimiento
			$movimientoNew = new MovimientoGarantia;
			
			$movimientoNew->garantia_id      = $request->garantia_id;
			$movimientoNew->estado           = 'RE';
			$movimientoNew->observacion      = str_replace(PHP_EOL,"<br>",$request->observacion);
			$movimientoNew->fechaVencimiento = $movimiento->fechaVencimiento;
			$movimientoNew->user_id          = Auth::user()->id;
				
			$movimientoNew->save();
			
			//almacena datos en ABEX
			try {
				$result = DB::connection('oracle')->update("UPDATE GARANTIAS SET ESTADO = 'RE', ESTADO_NOMBRE = 'Rechazado', FECHA_MODIFICADO = '".$movimientoNew->created_at."' WHERE ID = ".$movimientoNew->garantia_id);	
			}
			catch(Exception $e) {}
			//fin datos en ABEX
			
			if ($request->flujo == 1) { //secretaria de convenios
				return redirect('garantias/oficinaPartes/nomina')->with('message','rechazo');
			}
			elseif ($request->flujo == 2) { //secretaria de convenios
				return redirect('garantias/convenios')->with('message','rechazo');
			}
			elseif ($request->flujo == 3) { //convenios
				return redirect('garantias/abastecimiento')->with('message','rechazo');
			}
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Función que Llama al formulario de renovación
	 * Vista: garantia.renovar
	 * Rol: abastecimiento - convenio
     *
     * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @param  int  $flujo Flujo que renueva documento (2 - Convenios; 3 - Abastecimiento)
     * @return \Illuminate\Http\Response
     */
    public function renovar(Request $request, $id, $flujo)
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			
			$garantia = Garantia::find($id);
			
			$mov = MovimientoGarantia::where([['garantia_id',$id],['active',1]])
				   ->select('estado as estado')
				   ->selectRaw('DATE_FORMAT(fechaVencimiento, "%d-%m-%Y") as fechaVencimiento')
				   ->first(); 

			if ( Auth::user()->isRole('Convenios') && $flujo == 2 && ($mov->estado != 'NP' && $mov->estado != 'RN') ) { //documento no disponible para renovar por convenios
				return view('home');
			}
			
			if ( Auth::user()->isRole('Abastecimiento') && $flujo == 3 && ($mov->estado != 'NP' && $mov->estado != 'RN') ) { //documento no disponible para renovar por abastecimiento
				return view('home');
			}
			
			if ( $garantia->establecimiento_id == $estab_user ) {
				
				$proveedor = Proveedor::find($garantia->proveedor_id);
				$tipo      = TipoDoc::find($garantia->tipoDoc_id);
				$banco     = Banco::find($garantia->banco_id);
				
				return view('garantia.renovar',compact('garantia','proveedor','tipo','banco','flujo','mov'));
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
     * Función que registra el movimiento de Renovación de documentos
	 * Rol: abastecimiento - convenio
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function movRenovar(Request $request) 
	{
		if (Auth::check()) {
			
			$mov = MovimientoGarantia::where([['garantia_id',$request->garantia_id],['active',1]])->first();
			
			//cambia estado de movimiento de activo a no activo
			$id = $mov->id; 
			$movimiento = MovimientoGarantia::find($id);
			$movimiento->active = 0;
			
			$movimiento->save();
			
			//guarda nuevo movimiento
			$movimientoNew = new MovimientoGarantia;
			
			$fechaVencimiento = DateTime::createFromFormat('d-m-Y', $request->fechaVencimiento);
			
			$movimientoNew->garantia_id      = $request->garantia_id;
			$movimientoNew->estado           = 'RN';
			$movimientoNew->observacion      = str_replace(PHP_EOL,"<br>",$request->observacion);
			$movimientoNew->fechaVencimiento = $fechaVencimiento;
			$movimientoNew->user_id          = Auth::user()->id;
				
			$movimientoNew->save();
			
			//almacena datos en ABEX
			try {
				$result = DB::connection('oracle')->update("UPDATE GARANTIAS SET ESTADO = 'RN', ESTADO_NOMBRE = 'Renovado', FECHA_MODIFICADO = '".$movimientoNew->created_at."' WHERE ID = ".$movimientoNew->garantia_id);	
			}
			catch(Exception $e) {}
			//fin datos en ABEX
			
			if ($request->flujo == 2) { //secretaria de convenios
				return redirect('garantias/convenios')->with('message','renovado');
			}
			elseif ($request->flujo == 3) { //convenios
				return redirect('garantias/abastecimiento')->with('message','renovado');
			}
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
     * Llama al formulario de visualización de documentos adjuntos
	 * Vista: garantia.adjuntos
	 * Rol: None
     *
     * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function adjuntos(Request $request, $id)
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
		
			
			$garantia = Garantia::find($id);
			
			$mov = MovimientoGarantia::where([['garantia_id',$id],['active',1]])
				   ->select('estado as estado')
				   ->selectRaw('DATE_FORMAT(fechaVencimiento, "%d-%m-%Y") as fechaVencimiento')
				   ->first(); 

			if ( $garantia->establecimiento_id == $estab_user ) {
				
				$proveedor = Proveedor::find($garantia->proveedor_id);
				$tipo      = TipoDoc::find($garantia->tipoDoc_id);
				$banco     = Banco::find($garantia->banco_id);
				$objeto    = ObjetoGarantia::find($garantia->objeto_garantia_id);
				
				return view('garantia.adjuntos',compact('garantia','proveedor','tipo','banco','flujo','mov','objeto'));
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
     * Función que Muestra la Memo de Convenios
	 * Vista: garantia.memoPdf
	 * Rol: None
     *
	 * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function memoPdf($id) {
		if (Auth::check()) {
			$garantia   = Garantia::find($id);
			$proveedor  = Proveedor::find($garantia->proveedor_id);
			$moneda     = Moneda::find($garantia->moneda_id);
			$objeto     = ObjetoGarantia::find($garantia->objeto_garantia_id);
			$movimiento = MovimientoGarantia::where([['garantia_id',$id],['estado','TE']])->latest()->first(); 
			
			//determina firmante
			$fechaMov   = DateTime::createFromFormat('Y-m-d H:i:s', $movimiento->created_at);
			$fechaMov->setTime(0, 0);
			
			$firmante   = Firmante::where([['establecimiento_id',$garantia->establecimiento_id],
			                               ['memo_id',1],
										   ['active',1],
										   ['fechaDesde','<=',$fechaMov],
										   ['fechaHasta','>=',$fechaMov]])->first();
			$user1      = User::find($firmante->user_id);
			
			//determina receptor
			$firmante2  = Firmante::where([['establecimiento_id',$garantia->establecimiento_id],
			                               ['memo_id',2],
										   ['active',1],
										   ['fechaDesde','<=',$fechaMov],
										   ['fechaHasta','>=',$fechaMov]])->first();
			$user2      = User::find($firmante2->user_id);
			
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
			       ->loadView('garantia.memoPdf',compact('garantia','proveedor','moneda','fecha','movimiento','objeto','user1','user2'));
			return $pdf->stream();
		}
		else {
			return view('auth/login');
		}		
	}

	/**
     * Reporte de documentos
	 * Vista: garantia.reporte
	 * Rol: None
     *
     * @return \Illuminate\Http\Response
     */
	public function reporte(Request $request) 
	{
		if (Auth::check()) {
			
			$tipos = TipoDoc::where([['flujo',2],['active',1]])->orderBy('name')->get();
			
			$objetos = ObjetoGarantia::where('active',1)->orderBy('name')->get();
			
			return view('garantia.reporte',compact('tipos','objetos'));	
		}
		else {
			return view('auth/login');
		}
	}	
	
	/**
     * Fnución que muestra el Resultado de reporte de documentos
	 * Vista: garantia.resultado
	 * Rol: None
     *
	 * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function resultado(Request $request) 
	{
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			
			$garantias = DB::table('garantias')
							->join('proveedors','garantias.proveedor_id','=','proveedors.id')
							->join('tipo_docs','garantias.tipoDoc_id','=','tipo_docs.id')
							->join('bancos','garantias.banco_id','=','bancos.id')
							->join('monedas','garantias.moneda_id','=','monedas.id')
							->join('objeto_garantias','garantias.objeto_garantia_id','=','objeto_garantias.id')
							->join('movimiento_garantias', 'garantias.id','=','movimiento_garantias.garantia_id')
							->select('proveedors.rut as rut',
									 'proveedors.name as nameProveedor',
									 'tipo_docs.name as tipoDoc',
									 'bancos.name as banco',
									 'monedas.name as moneda',
									 'objeto_garantias.name as objeto_garantia',
									 'garantias.id as id',
									 'garantias.nDoc as nDoc',
									 'garantias.monto as monto',
									 'garantias.nomina as nomina',
									 'garantias.licitacion as licitacion',
									 'garantias.archivo as archivo',
									 'garantias.estado as estadoDev',
									 'movimiento_garantias.estado as estado',
									 'movimiento_garantias.observacion as observacion')
							->selectRaw('DATE_FORMAT(garantias.fechaRecepcion, "%d-%m-%Y") as fechaRecepcion')
							->selectRaw('DATE_FORMAT(garantias.fechaEmision, "%d-%m-%Y") as fechaEmision')
							->selectRaw('DATE_FORMAT(movimiento_garantias.fechaVencimiento, "%d-%m-%Y") as fechaVencimiento')
							->orderBy('garantias.id')
							->where([['garantias.establecimiento_id','=',$estab_user],
									 ['movimiento_garantias.active',1]]);
							
			if( $request->get('proveedor') != null ) {
				//determina el ID del proveedor
				$explode = explode("-",$request->get('proveedor'));
				$rut = $explode[0];
				
				$garantias = $garantias->where('proveedors.rut',$rut);
			}
			
			if( $request->get('nomDoc') != null ) {
				$garantias = $garantias->where('garantias.nDoc',$request->get('nomDoc'));
			}
			
			if( $request->get('objeto') != null ) {
				$garantias = $garantias->where('garantias.objeto_garantia_id',$request->get('objeto'));
			}
			
			if( $request->get('tipo') != null ) {
				$garantias = $garantias->where('garantias.tipoDoc_id',$request->get('tipo'));
			}
			
			if( $request->get('desde') != null ) {
				//formatea fechas
				$fecha = DateTime::createFromFormat('d-m-Y H:i:s', $request->get('desde')." 00:00:00");
				
				$garantias = $garantias->where('garantias.fechaRecepcion','>=',$fecha);
			}
			
			if( $request->get('hasta') != null ) {
				//formatea fechas
				$fecha = DateTime::createFromFormat('d-m-Y H:i:s', $request->get('hasta')." 23:59:59");
								
				$garantias = $garantias->where('garantias.fechaRecepcion','<=',$fecha);
			}
			
			if( $request->get('estado') != null ) {
				$garantias = $garantias->where('movimiento_garantias.estado',$request->get('estado'));
			}
			
			$garantias = $garantias->paginate(10)
			->appends('proveedor',$request->get('proveedor'))
			->appends('nomDoc',$request->get('nomDoc'))
			->appends('objeto',$request->get('objeto'))
			->appends('tipo',$request->get('tipo'))
			->appends('desde',$request->get('desde'))
			->appends('hasta',$request->get('hasta'))
			->appends('estado',$request->get('estado'));			
			
			return view('garantia.resultado',compact('garantias'));
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
	 *
     * @param  \Illuminate\Http\Request  $request	 
	 * @return list lista de proveedores que coinciden con la busqueda
	 */
	public function autoComplete(Request $request) 
	{
        $query = $request->get('term','');
        
        $proveedors=Proveedor::where([['rut','LIKE','%'.$query.'%'],['active','1']])->orderBy('name')->get();
        
        $data=array();
        foreach ($proveedors as $proveedor) {
				$data[]=array('value'=>$proveedor->rut."-".$proveedor->dv." ".$proveedor->name);
        }
        if(count($data))
             return $data;
        else
            return ['value'=>'Proveedor no encontrado'];
    }	
	
}
