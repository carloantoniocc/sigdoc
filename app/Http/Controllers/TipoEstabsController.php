<?php

namespace sigdoc\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

use sigdoc\TipoEstab;

use Illuminate\Support\Facades\Auth;

/**
 * Clase Controlador Tipo de Establecimientos
 * Rol: Administrador
 */
class TipoEstabsController extends Controller
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
     * Vista: tipoEstabs.index
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
		if (Auth::check()) {
			$tipos = TipoEstab::select('id','name','active')->orderBy('name')->paginate(10);
			
			return view('tipoEstabs.index',compact('tipos'));
		}
		else {
			return view('auth/login');
		}
		
    }

    /**
     * Show the form for creating a new resource.
     * Vista: tipoEstabs.create
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
		if (Auth::check()) {
			return view('tipoEstabs.create');		
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
				return redirect('tipoEstabs/create')
							->withErrors($validator)
							->withInput();
			}
			else {
				$tipoEstab = new TipoEstab;
				
				$tipoEstab->name = $request->input('name');
				$tipoEstab->active = $request->input('active');
			
				$tipoEstab->save();			
				
				return redirect('/tipoEstabs')->with('message','store');
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
     * Vista: tipoEstabs.edit
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
		if (Auth::check()) {
			$tipo = TipoEstab::find($id);
			
			return view('tipoEstabs.edit',compact('tipo'));
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
				'name' => 'required|string|max:150|unique:tipo_estabs,name,'.$id,
			]);
			
			if ($validator->fails()) {
				return redirect('tipoEstabs/'.$id.'/edit')
							->withErrors($validator)
							->withInput();
			}
			else {
				$tipo = TipoEstab::find($id);
				
				$tipo->name = $request->input('name');
				$tipo->active = $request->input('active');
			
				$tipo->save();			
				
				return redirect('/tipoEstabs')->with('message','update');
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
