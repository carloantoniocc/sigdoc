<?php

namespace sigdoc\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

use sigdoc\Establecimiento;
use sigdoc\Comuna;
use sigdoc\TipoEstab;

use Illuminate\Support\Facades\Auth;

/**
 * Clase Controlador Establecimientos
 * Rol: Administrador
 */
class EstablecimientosController extends Controller
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
	 * Vista: establecimientos.index
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
		if (Auth::check()) {
			$establecimientos = Establecimiento::select('id','code','name','active')->orderBy('name')->paginate(10);
			
			return view('establecimientos.index',compact('establecimientos'));
		}
		else {
			return view('auth/login');
		}
		
    }

    /**
     * Show the form for creating a new resource.
	 * Vista: establecimientos.create
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
		if (Auth::check()) {
			$comunas     = Comuna::where('active',1)->orderBy('name')->get();
			$tipos       = TipoEstab::where('active',1)->orderBy('name')->get();;
			
			return view('establecimientos.create',compact('comunas','tipos'));
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
				'code' => 'required|string|max:150|unique:establecimientos',
				'name' => 'required|string|max:150|unique:establecimientos',
				'tipo' => 'required',
				'comuna' => 'required',
				'direccion' => 'required|string|max:150',
			]);
			
			if ($validator->fails()) {
				return redirect('establecimientos/create')
							->withErrors($validator)
							->withInput();
			}
			else {
				$establecimiento = new Establecimiento;
				
				$establecimiento->code = $request->input('code');
				$establecimiento->name = $request->input('name');
				$establecimiento->tipo_id = $request->input('tipo');
				$establecimiento->comuna_id = $request->input('comuna');
				$establecimiento->direccion = $request->input('direccion');
				$establecimiento->x = $request->input('x');
				$establecimiento->y = $request->input('y');
				$establecimiento->flujo = $request->input('flujo');
				$establecimiento->active = $request->input('active');
			
				$establecimiento->save();			
				
				return redirect('/establecimientos')->with('message','store');
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
	 * Vista: establecimientos.edit
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::check()) {
			$establecimiento = Establecimiento::find($id);
			
			$comunas     = Comuna::where('active',1)->orderBy('name')->get();
			$tipos       = TipoEstab::where('active',1)->orderBy('name')->get();;
			
			return view('establecimientos.edit',compact('establecimiento','comunas','tipos'));
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
				'code' => 'required|string|max:150|unique:establecimientos,code,'.$id,
				'name' => 'required|string|max:150|unique:establecimientos,name,'.$id,
				'tipo' => 'required',
				'comuna' => 'required',
				'direccion' => 'required|string|max:150',
			]);
			
			if ($validator->fails()) {
				return redirect('establecimientos/'.$id.'/edit')
							->withErrors($validator)
							->withInput();
			}
			else {
				$establecimiento = Establecimiento::find($id);
				
				$establecimiento->code = $request->input('code');
				$establecimiento->name = $request->input('name');
				$establecimiento->tipo_id = $request->input('tipo');
				$establecimiento->comuna_id = $request->input('comuna');
				$establecimiento->direccion = $request->input('direccion');
				$establecimiento->x = $request->input('x');
				$establecimiento->y = $request->input('y');
				$establecimiento->flujo = $request->input('flujo');
				$establecimiento->active = $request->input('active');
			
				$establecimiento->save();			
				
				return redirect('/establecimientos')->with('message','update');
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
