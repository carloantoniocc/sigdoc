<?php

namespace sigdoc\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

use sigdoc\TipoPago;

use Illuminate\Support\Facades\Auth;

/**
 * Clase Controlador Tipo de Pagos
 * Rol: Administrador
 */
class TipoPagosController extends Controller
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
     * Vista: tipoPagos.index
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
		if (Auth::check()) {
			$tipos = TipoPago::select('id','name','entrega','active')->orderBy('name')->paginate(10);
			
			return view('tipoPagos.index',compact('tipos'));
		}
		else {
			return view('auth/login');
		}
    }

    /**
     * Show the form for creating a new resource.
     * Vista: tipoPagos.create
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
		if (Auth::check()) {
			return view('tipoPagos.create');		
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
				'name' => 'required|string|max:150|unique:tipo_pagos',
			]);
			
			if ($validator->fails()) {
				return redirect('tipoPagos/create')
							->withErrors($validator)
							->withInput();
			}
			else {
				$tipoPago = new TipoPago;
				
				$tipoPago->name     = $request->input('name');
				$tipoPago->entrega  = $request->input('entrega');
				$tipoPago->id_sigfe = $request->input('tipoSigfe');
				$tipoPago->active   = $request->input('active');
			
				$tipoPago->save();			
				
				return redirect('/tipoPagos')->with('message','store');
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
     * Vista: tipoPagos.edit
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::check()) {
			$tipo = TipoPago::find($id);
			
			return view('tipoPagos.edit',compact('tipo'));
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
				'name' => 'required|string|max:150|unique:tipo_pagos,name,'.$id,
			]);
			
			if ($validator->fails()) {
				return redirect('tipoPagos/'.$id.'/edit')
							->withErrors($validator)
							->withInput();
			}
			else {
				$tipo = TipoPago::find($id);
				
				$tipo->name    = $request->input('name');
				$tipo->entrega = $request->input('entrega');
				$tipo->id_sigfe = $request->input('tipoSigfe');
				$tipo->active  = $request->input('active');
			
				$tipo->save();			
				
				return redirect('/tipoPagos')->with('message','update');
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
