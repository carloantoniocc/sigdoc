<?php

namespace sigdoc\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

use sigdoc\ObjetoGarantia;

use Illuminate\Support\Facades\Auth;

/**
 * Clase Controlador Objetos de Garantía
 * Rol: Administrador
 */
class ObjetoGarantiasController extends Controller
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
     * Vista: objetoGarantias.index
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
		if (Auth::check()) {
			$objetos = ObjetoGarantia::select('id','name','flujo','active')->orderBy('name')->paginate(10);
			
			return view('objetoGarantias.index',compact('objetos'));
		}
		else {
			return view('auth/login');
		}
    }

    /**
     * Show the form for creating a new resource.
     * Vista: objetoGarantias.create 
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
		if (Auth::check()) {
			return view('objetoGarantias.create');		
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
				'name' => 'required|string|max:150|unique:tipo_estabs',
			]);
			
			if ($validator->fails()) {
				return redirect('objetoGarantias/create')
							->withErrors($validator)
							->withInput();
			}
			else {
				$objeto = new ObjetoGarantia;
				
				$objeto->name   = $request->input('name');
				$objeto->flujo  = $request->input('flujo');
				$objeto->active = $request->input('active');
			
				$objeto->save();			
				
				return redirect('/objetoGarantias')->with('message','store');
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
     * Vista: objetoGarantias.edit
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::check()) {
			$objeto = ObjetoGarantia::find($id);
			
			return view('objetoGarantias.edit',compact('objeto'));
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
				'name' => 'required|string|max:150|unique:objeto_garantias,name,'.$id,
			]);
			
			if ($validator->fails()) {
				return redirect('objetoGarantias/'.$id.'/edit')
							->withErrors($validator)
							->withInput();
			}
			else {
				$objeto = ObjetoGarantia::find($id);
				
				$objeto->name   = $request->input('name');
				$objeto->flujo  = $request->input('flujo');
				$objeto->active = $request->input('active');
			
				$objeto->save();			
				
				return redirect('/objetoGarantias')->with('message','update');
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
