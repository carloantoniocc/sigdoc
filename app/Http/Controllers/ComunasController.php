<?php

namespace sigdoc\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

use sigdoc\Comuna;

use Illuminate\Support\Facades\Auth;

/**
 * Clase Controlador Comunas
 * Rol: Administrador
 */
class ComunasController extends Controller
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
     * Vista: comunas.index
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
		if (Auth::check()) {
			$comunas = Comuna::select('id','name','active')->orderBy('name')->paginate(10);
			
			return view('comunas.index',compact('comunas'));
		}
		else {
			return view('auth/login');
		}
    }

    /**
     * Show the form for creating a new resource.
     * Vista: comunas.create
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
		if (Auth::check()) {
			return view('comunas.create');		
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
				'name' => 'required|string|max:150|unique:comunas',
			]);
			
			if ($validator->fails()) {
				return redirect('comunas/create')
							->withErrors($validator)
							->withInput();
			}
			else {
				$comuna = new Comuna;
				
				$comuna->name = $request->input('name');
				$comuna->active = $request->input('active');
			
				$comuna->save();			
				
				return redirect('/comunas')->with('message','store');
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
     * Vista: comunas.edit
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::check()) {
			$comuna = Comuna::find($id);
			
			return view('comunas.edit',compact('comuna'));
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
				'name' => 'required|string|max:150|unique:comunas,name,'.$id,
			]);
			
			if ($validator->fails()) {
				return redirect('comunas/'.$id.'/edit')
							->withErrors($validator)
							->withInput();
			}
			else {
				$comuna = Comuna::find($id);
				
				$comuna->name = $request->input('name');
				$comuna->active = $request->input('active');
			
				$comuna->save();			
				
				return redirect('/comunas')->with('message','update');
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
