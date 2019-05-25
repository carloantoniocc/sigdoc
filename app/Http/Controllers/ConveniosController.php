<?php

namespace sigdoc\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

use sigdoc\Convenio;
use sigdoc\Proveedor;
use sigdoc\TipoCompra;
use sigdoc\Referente;
use sigdoc\Validador;
use sigdoc\User;

use DB;
use Illuminate\Support\Facades\Auth;
/**
 * Clase Controlador Convenios
 * Rol: Convenio
 */
class ConveniosController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
		
		//Controladores de usuarios
        $this->middleware('convenios');
    }  
	
    /**
     * Display a listing of the resource.
	 * Vista: factura.convenios.index
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;
			
			$convenios = DB::table('convenios')
						->join('proveedors','convenios.proveedor_id','=','proveedors.id')
						->join('tipo_compras','convenios.tipoCompra_id','=','tipo_compras.id')
						->join('referentes','convenios.referente_id','=','referentes.id')
						->select('convenios.id as id',
						         'proveedors.rut as rut', 'proveedors.name as nameProveedor',
								 'tipo_compras.name as nameTipoCompras',
								 'convenios.identificador as identificador',
								 'referentes.name as nameReferente',
								 'convenios.observacion as observacion',
								 'convenios.active as active')
						->where('convenios.establecimiento_id',$estab_user)
						->where([['rut', 'LIKE', '%'.$request->get('searchRut').'%'],
						         ['identificador', 'LIKE', '%'.$request->get('searchId').'%']])		 
						->orderBy('convenios.id')->paginate(10)
						->appends('searchId',$request->get('searchId'))
						->appends('searchRut',$request->get('searchRut'));
			
			return view('factura.convenios.index',compact('convenios'));
		}
		else {
			return view('auth/login');
		}		
    }

    /**
     * Show the form for creating a new resource.
	 * Vista: factura.convenios.create
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			$tipos       = TipoCompra::where('active',1)->orderBy('name')->get();
			$referentes  = Referente::where([['establecimiento_id',$estab_user],['active',1]])->orderBy('name')->get();
			
			return view('factura.convenios.create',compact('tipos','referentes'));
			
		}
		else {
			return view('auth/login');
		}
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			$validator = validator::make($request->all(), [
				'proveedor' => 'required',
				'tipo' => 'required',
				'identificador' => 'required|string|max:150',
				'referente' => 'required',
				'observacion' => 'required|string|max:150',
			]);
			
			if ($validator->fails()) {
				return redirect('/convenios/create')
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
					return redirect('/convenios/create')->with('message','proveedor')->withInput();
				}
				
				//guarda datos de convenios
				$convenio = new Convenio;
				
				$convenio->proveedor_id  	  = $proveedor->id;
				$convenio->tipoCompra_id 	  = $request->tipo;
				$convenio->identificador 	  = $request->identificador;
				$convenio->referente_id  	  = $request->referente;
				$convenio->establecimiento_id = $estab_user;
				$convenio->observacion   	  = $request->observacion;
				$convenio->active        	  = $request->active;
				
				$convenio->save();
				
				return redirect('/convenios')->with('message','store');
			}
		}
		else {
			return view('auth/login');
		}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
	 * Vista: factura.convenios.edit 
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;			
			
			$convenio = Convenio::find($id);
			
			if($convenio->establecimiento_id == $estab_user) {
				$tipos       = TipoCompra::where('active',1)->orderBy('name')->get();
				$referente   = Referente::find($convenio->referente_id);
				$proveedor   = Proveedor::find($convenio->proveedor_id);
			
				return view('factura.convenios.edit',compact('convenio','tipos','referente','proveedor'));
			}
			else {
				return redirect('/convenios');
			}
		}
		else {
			return view('auth/login');
		}
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (Auth::check()) {
			$validator = validator::make($request->all(), [
				'tipo' => 'required',
				'observacion' => 'required|string|max:150',
			]);
			
			if ($validator->fails()) {
				return redirect('/convenios/'.$id.'/edit')
							->withErrors($validator)
							->withInput();
			}
			else {
				//guarda datos de convenios
				$convenio = Convenio::find($id);
				
				$convenio->tipoCompra_id = $request->tipo;
				$convenio->observacion   = $request->observacion;
				$convenio->active        = $request->active;
				
				$convenio->save();
				
				return redirect('/convenios')->with('message','update');
			}
		}
		else {
			return view('auth/login');
		}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
	/*******************************************************************************************/
	/*                                         VALIDADOR                                       */
	/*******************************************************************************************/
	/**
	 * Función que llama vista para asignar validadores
	 * Vista: factura.convenios.validador
	 *
	 * @param  int  $id
     * @return \Illuminate\Http\Response
	 */
	public function	asignValidador($id) {
		if (Auth::check()) {
			//establecimiento usuario
			//$estab_user = $request->session()->get('establecimiento');
			$user       = User::find(Auth::user()->id);
			$estab_user = $user->establecimientos()->first()->id;				
			
			$validadors     = Validador::orderBy('name')->where('active',1)->get();
			$convenio       = Convenio::find($id);
			
			if($convenio->establecimiento_id == $estab_user) {
				return view('factura.convenios.validador',compact('convenio','validadors','id'));
			}
			else {
				return redirect('/convenios');
			}
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
	 * Funcion que guarda validadores
	 *
	 * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
	 */
	public function saveValidador(Request $request) {
		if (Auth::check()) {
			$id       = $request->input('id');
			$convenio = Convenio::find($id);
			
			//Graba nuevos roles asignados
			$validadores = $request->input('validadores');
			
			$convenio->validadores()->sync($validadores);
			
			return redirect('/convenios')->with('message','validadores');
		}
		else {
			return view('auth/login');
		}
	}
	
	
	/*******************************************************************************************/
	/*                                       AUTOCOMPLETAR                                     */
	/*******************************************************************************************/
	/**
	 * Funcion que autocompleta con campo de proveedores con RUT
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
