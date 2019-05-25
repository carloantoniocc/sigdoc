<?php

namespace sigdoc\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

use sigdoc\Validador;

use Illuminate\Support\Facades\Auth;

/**
 * Clase Controlador Tipo de Documentos Validadores
 * Rol: Administrador
 */
class ValidadorsController extends Controller
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
     * Vista: validadors.index
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
		if (Auth::check()) {
			$validadors = Validador::select('id','name','active')->orderBy('name')->paginate(10);
			
			return view('validadors.index',compact('validadors'));
		}
		else {
			return view('auth/login');
		}
    }

    /**
     * Show the form for creating a new resource.
     * Vista: validadors.create
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
		if (Auth::check()) {
			return view('validadors.create');		
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
				'name' => 'required|string|max:150|unique:validadors',
			]);
			
			if ($validator->fails()) {
				return redirect('validadors/create')
							->withErrors($validator)
							->withInput();
			}
			else {
				$validador = new Validador;
				
				$validador->name = $request->input('name');
				$validador->active = $request->input('active');
			
				$validador->save();			
				
				return redirect('/validadors')->with('message','store');
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
     * Vista: validadors.edit
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
		if (Auth::check()) {
			$validador = Validador::find($id);
			
			return view('validadors.edit',compact('validador'));
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
				'name' => 'required|string|max:150|unique:validadors,name,'.$id,
			]);
			
			if ($validator->fails()) {
				return redirect('validadors/'.$id.'/edit')
							->withErrors($validator)
							->withInput();
			}
			else {
				$validador = Validador::find($id);
				
				$validador->name = $request->input('name');
				$validador->active = $request->input('active');
			
				$validador->save();			
				
				return redirect('/validadors')->with('message','update');
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
