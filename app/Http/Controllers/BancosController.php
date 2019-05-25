<?php

namespace sigdoc\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

use sigdoc\Banco;

use Illuminate\Support\Facades\Auth;

/**
 * Clase Controlador Bancos
 * Rol: Administrador
 */
class BancosController extends Controller
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
     * Vista: bancos.index
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
		if (Auth::check()) {
			$bancos = Banco::select('id','name','active')->orderBy('name')->paginate(10);
			
			return view('bancos.index',compact('bancos'));
		}
		else {
			return view('auth/login');
		}
    }

    /**
     * Show the form for creating a new resource.
     * Vista: bancos.create
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
		if (Auth::check()) {
			return view('bancos.create');		
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
				'name' => 'required|string|max:150|unique:bancos',
			]);
			
			if ($validator->fails()) {
				return redirect('bancos/create')
							->withErrors($validator)
							->withInput();
			}
			else {
				$banco = new Banco;
				
				$banco->name = $request->input('name');
				$banco->active = $request->input('active');
			
				$banco->save();			
				
				return redirect('/bancos')->with('message','store');
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
     * Vista: bancos.edit
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::check()) {
			$banco = Banco::find($id);
			
			return view('bancos.edit',compact('banco'));
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
				'name' => 'required|string|max:150|unique:bancos,name,'.$id,
			]);
			
			if ($validator->fails()) {
				return redirect('bancos/'.$id.'/edit')
							->withErrors($validator)
							->withInput();
			}
			else {
				$banco = Banco::find($id);
				
				$banco->name = $request->input('name');
				$banco->active = $request->input('active');
			
				$banco->save();			
				
				return redirect('/bancos')->with('message','update');
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
