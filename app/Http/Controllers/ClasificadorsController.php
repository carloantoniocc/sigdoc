<?php

namespace sigdoc\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

use sigdoc\Clasificador;

use Illuminate\Support\Facades\Auth;

/**
 * Clase Controlador Clasificador Presupuestario
 * Rol: Administrador
 */
class ClasificadorsController extends Controller
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
     * Vista: clasificadors.index
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
		if (Auth::check()) {
			$clasificadors = Clasificador::select('id','codigo','name','active')->orderBy('name')->paginate(10);
			
			return view('clasificadors.index',compact('clasificadors'));
		}
		else {
			return view('auth/login');
		}		
    }

    /**
     * Show the form for creating a new resource.
     * Vista: clasificadors.create
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
		if (Auth::check()) {
			return view('clasificadors.create');		
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
				'codigo' => 'required|string|max:150|unique:clasificadors',
				'name'   => 'required|string|max:150|unique:clasificadors',
			]);
			
			if ($validator->fails()) {
				return redirect('clasificadors/create')
							->withErrors($validator)
							->withInput();
			}
			else {
				$clasificador = new Clasificador;
				
				$clasificador->codigo = $request->input('codigo');
				$clasificador->name   = $request->input('name');
				$clasificador->active = $request->input('active');
			
				$clasificador->save();			
				
				return redirect('/clasificadors')->with('message','store');
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
     * Vista: clasificadors.edit
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::check()) {
			$clasificador = Clasificador::find($id);
			
			return view('clasificadors.edit',compact('clasificador'));
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
				'codigo' => 'required|string|max:150|unique:clasificadors,codigo,'.$id,
				'name'   => 'required|string|max:150|unique:clasificadors,name,'.$id,
			]);
			
			if ($validator->fails()) {
				return redirect('clasificadors/'.$id.'/edit')
							->withErrors($validator)
							->withInput();
			}
			else {
				$clasificador = Clasificador::find($id);
				
				$clasificador->codigo = $request->input('codigo');
				$clasificador->name = $request->input('name');
				$clasificador->active = $request->input('active');
			
				$clasificador->save();			
				
				return redirect('/clasificadors')->with('message','update');
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
