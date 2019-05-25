<?php

namespace sigdoc\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

use sigdoc\Moneda;

use Illuminate\Support\Facades\Auth;

/**
 * Clase Controlador Monedas
 * Rol: Administrador
 */
class MonedasController extends Controller
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
     * Vista: monedas.index
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
		if (Auth::check()) {
			$monedas = Moneda::select('id','name','active')->orderBy('name')->paginate(10);
			
			return view('monedas.index',compact('monedas'));
		}
		else {
			return view('auth/login');
		}
    }

    /**
     * Show the form for creating a new resource.
     * Vista: monedas.create
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
		if (Auth::check()) {
			return view('monedas.create');		
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
				'name' => 'required|string|max:150|unique:monedas',
			]);
			
			if ($validator->fails()) {
				return redirect('monedas/create')
							->withErrors($validator)
							->withInput();
			}
			else {
				$moneda = new Moneda;
				
				$moneda->name = $request->input('name');
				$moneda->active = $request->input('active');
			
				$moneda->save();			
				
				return redirect('/monedas')->with('message','store');
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
     * Vista: monedas.edit 
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::check()) {
			$moneda = Moneda::find($id);
			
			return view('monedas.edit',compact('moneda'));
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
				'name' => 'required|string|max:150|unique:monedas,name,'.$id,
			]);
			
			if ($validator->fails()) {
				return redirect('monedas/'.$id.'/edit')
							->withErrors($validator)
							->withInput();
			}
			else {
				$moneda = Moneda::find($id);
				
				$moneda->name = $request->input('name');
				$moneda->active = $request->input('active');
			
				$moneda->save();			
				
				return redirect('/monedas')->with('message','update');
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
