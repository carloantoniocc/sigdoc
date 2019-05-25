<?php

namespace sigdoc\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

use sigdoc\Cuenta;

use Illuminate\Support\Facades\Auth;

/**
 * Clase Controlador Cuentas de Banco
 * Rol: Administrador
 */
class CuentasController extends Controller
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
     * Vista: cuentas.index
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
		if (Auth::check()) {
			$cuentas = Cuenta::select('id','name','active')->orderBy('name')->paginate(10);
			
			return view('cuentas.index',compact('cuentas'));
		}
		else {
			return view('auth/login');
		}		
    }

    /**
     * Show the form for creating a new resource.
     * Vista: cuentas.create
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
		if (Auth::check()) {
			return view('cuentas.create');		
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
				'name' => 'required|string|max:150|unique:cuentas',
			]);
			
			if ($validator->fails()) {
				return redirect('cuentas/create')
							->withErrors($validator)
							->withInput();
			}
			else {
				$cuenta = new Cuenta;
				
				$cuenta->name = $request->input('name');
				$cuenta->active = $request->input('active');
			
				$cuenta->save();			
				
				return redirect('/cuentas')->with('message','store');
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
     * Vista: cuentas.edit
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::check()) {
			$cuenta = Cuenta::find($id);
			
			return view('cuentas.edit',compact('cuenta'));
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
				'name' => 'required|string|max:150|unique:cuentas,name,'.$id,
			]);
			
			if ($validator->fails()) {
				return redirect('cuentas/'.$id.'/edit')
							->withErrors($validator)
							->withInput();
			}
			else {
				$cuenta = Cuenta::find($id);
				
				$cuenta->name = $request->input('name');
				$cuenta->active = $request->input('active');
			
				$cuenta->save();			
				
				return redirect('/cuentas')->with('message','update');
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
