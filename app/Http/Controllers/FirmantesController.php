<?php

namespace sigdoc\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

use sigdoc\Firmante;
use sigdoc\User;
use sigdoc\Establecimiento;

use DateTime;
use DB;
use Illuminate\Support\Facades\Auth;

/**
 * Clase Controlador Usuarios Firmantes
 * Rol: Administrador
 */
class FirmantesController extends Controller
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
        $this->middleware('admin');
    } 
    
	/**
     * Display a listing of the resource.
	 * Vista: firmantes.index
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		//
		if (Auth::check()) {
			$firmantes = DB::table('firmantes')
			             ->join('establecimientos','firmantes.establecimiento_id','=','establecimientos.id')
						 ->join('users','firmantes.user_id','=','users.id')
						 ->select('users.name as usuario',
						          'establecimientos.name as establecimiento',
								  'firmantes.memo_id as memo_id',
								  'firmantes.active as active',
								  'firmantes.id as id',
								  'firmantes.fechaDesde as fechaDesde',
								  'firmantes.fechaHasta as fechaHasta') 
						 ->orderBy('users.name')->paginate(10);
			return view('firmantes.index',compact('firmantes'));
		}
		else {
			return view('auth/login');
		}

    }

    /**
     * Show the form for creating a new resource.
	 * Vista: firmantes.create
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
		if (Auth::check()) {
			$establecimientos = Establecimiento::where('active',1)->orderBy('name')->get();
			$usuarios         = User::where('active',1)->orderBy('name')->get();
			
			return view('firmantes.create',compact('establecimientos','usuarios'));
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
			// validate
			$validator = validator::make($request->all(), [
				'memo_id' => 'required',
				'user_id' => 'required',
				'establecimiento_id' => 'required',
				'fechaDesde' => 'required',
				'fechaHasta' => 'required',
			]);
			
			if ($validator->fails()) {
				return redirect('firmantes/create')
							->withErrors($validator)
							->withInput();
			}
			else {
				$firmantes = new Firmante;
				
				$fechaDesde = DateTime::createFromFormat('d-m-Y', $request->fechaDesde);
				$fechaHasta = DateTime::createFromFormat('d-m-Y', $request->fechaHasta);
				
				$firmantes->memo_id            = $request->input('memo_id');
				$firmantes->user_id            = $request->input('user_id');
				$firmantes->establecimiento_id = $request->input('establecimiento_id');
				$firmantes->fechaDesde         = $fechaDesde;
				$firmantes->fechaHasta         = $fechaHasta;
				$firmantes->active             = $request->input('active');
				
				$firmantes->save();			
				
				return redirect('/firmantes')->with('message','store');
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
	 * Vista: firmantes.edit
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::check()) {
			$firmante = Firmante::where('id',$id)
						->select('id',
						         'memo_id',
								 'user_id',
								 'establecimiento_id',
								 'active')
						->selectRaw('DATE_FORMAT(fechaDesde, "%d-%m-%Y") as fechaDesde')		 
						->selectRaw('DATE_FORMAT(fechaHasta, "%d-%m-%Y") as fechaHasta')		 
						->first();
			
			$establecimiento = Establecimiento::find($firmante->establecimiento_id);
			$usuario         = User::find($firmante->user_id);
			
			return view('firmantes.edit',compact('firmante','establecimiento','usuario'));
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
			// validate
			$validator = validator::make($request->all(), [
				'fechaDesde' => 'required',
				'fechaHasta' => 'required',
			]);
			
			if ($validator->fails()) {
				return redirect('firmantes/'.$id.'/edit')
							->withErrors($validator)
							->withInput();
			}
			else {
				$firmante = Firmante::find($id);
				
				$fechaDesde = DateTime::createFromFormat('d-m-Y', $request->fechaDesde);
				$fechaHasta = DateTime::createFromFormat('d-m-Y', $request->fechaHasta);
				
				$firmante->fechaDesde = $fechaDesde;
				$firmante->fechaHasta = $fechaHasta;
				$firmante->active      = $request->input('active');
			
				$firmante->save();			
				
				return redirect('/firmantes')->with('message','update');
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
}
