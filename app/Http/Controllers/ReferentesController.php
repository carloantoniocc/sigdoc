<?php

namespace sigdoc\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

use sigdoc\Referente;
use sigdoc\Establecimiento;

use DB;
use Illuminate\Support\Facades\Auth;

/**
 * Clase Controlador Referentes
 * Rol: Administrador
 */
class ReferentesController extends Controller
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
     * Vista: referentes.index
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
		if (Auth::check()) {
			$referentes = DB::table('referentes')
						->join('establecimientos','referentes.establecimiento_id','=','establecimientos.id')
						->select('referentes.id as id',
								 'referentes.name as name',
								 'referentes.active as active',
								 'establecimientos.name as establecimiento')
						->orderBy('referentes.name')->paginate(10);
			
			return view('referentes.index',compact('referentes'));
		}
		else {
			return view('auth/login');
		}		
    }

    /**
     * Show the form for creating a new resource.
     * Vista: referentes.create
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
		if (Auth::check()) {
			$establecimientos = Establecimiento::where('active',1)->orderBy('name')->get();
			
			return view('referentes.create',compact('establecimientos'));		
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
				'name' => 'required|string|max:150|unique:referentes',
			]);
			
			if ($validator->fails()) {
				return redirect('referentes/create')
							->withErrors($validator)
							->withInput();
			}
			else {
				$referente = new Referente;
				
				$referente->name = $request->input('name');
				$referente->establecimiento_id = $request->input('establecimiento');
				$referente->active = $request->input('active');
			
				$referente->save();			
				
				return redirect('/referentes')->with('message','store');
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
     * Vista: referentes.edit
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::check()) {
			$referente = Referente::find($id);
			$establecimiento = Establecimiento::find($referente->establecimiento_id);
			
			return view('referentes.edit',compact('referente','establecimiento'));
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
				'name' => 'required|string|max:150|unique:referentes,name,'.$id,
			]);
			
			if ($validator->fails()) {
				return redirect('referentes/'.$id.'/edit')
							->withErrors($validator)
							->withInput();
			}
			else {
				$referente = Referente::find($id);

				$referente->active = $request->input('active');
			
				$referente->save();			
				
				return redirect('/referentes')->with('message','update');
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
