<?php

namespace sigdoc\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

use sigdoc\Proveedor;
use sigdoc\Comuna;

use DB;
use Illuminate\Support\Facades\Auth;

/**
 * Clase Controlador Proveedores
 * Rol: Administrador
 */
class ProveedorsController extends Controller
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
        $this->middleware('proveedor');
    } 
    
	/**
     * Display a listing of the resource.
	 * Vista: proveedors.index
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
		if (Auth::check()) {
			$proveedors = Proveedor::searchNombre($request->get('searchNombre'))
			                       ->searchRut($request->get('searchRut'))
								   ->select('id','name','rut','dv','active')
								   ->orderBy('name')
								   ->paginate(10)
								   ->appends('searchNombre',$request->get('searchNombre'))
								   ->appends('searchRut',$request->get('searchRut'));
			
			return view('proveedors.index',compact('proveedors'));
		}
		else {
			return view('auth/login');
		}
    }

    /**
     * Show the form for creating a new resource.
	 * Vista: proveedors.create
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
		if (Auth::check()) {
			$comunas  = Comuna::where('active',1)->orderBy('name')->get();
			$req      = 0; //valor para determinar a que pantalla vuelve despues de crear el proveedor
			
			return view('proveedors.create',compact('comunas'));
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
			//valida si rut existe
			$validator2 = validator::make($request->all(), [
				'rut' => 'required|integer|unique:proveedors',	
			]);
			if ($validator2->fails()) {
				return redirect('proveedors/create')
							->with('message','rut')
							->withInput();
			} 
						
			// valida otros campos
			$validator = validator::make($request->all(), [
				'name' => 'required|string|max:150|unique:proveedors',
				'fantasia' => 'nullable|string|max:150',
				'comuna' => 'required',
				'direccion' => 'required|string|max:150',
				'telefono' => 'required|string|max:150',
				'email' => 'nullable|string|max:150',
			]);
			
			if ($validator->fails()) {
				return redirect('proveedors/create')
							->withErrors($validator)
							->withInput();
			}
			else {
				$proveedor = new Proveedor;
				
				$proveedor->rut = $request->input('rut');
				$proveedor->dv = $request->input('dv');
				$proveedor->name = $request->input('name');
				$proveedor->fantasia = $request->input('fantasia');
				$proveedor->comuna_id = $request->input('comuna');
				$proveedor->direccion = $request->input('direccion');
				$proveedor->x = $request->input('x');
				$proveedor->y = $request->input('y');
				$proveedor->telefono = $request->input('telefono');
				$proveedor->email = $request->input('email');
				$proveedor->active = $request->input('active');
			
				$proveedor->save();			
				
				return redirect('/proveedors')->with('message','store');
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
	 * Vista: proveedors.edit
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::check()) {
			$proveedor = Proveedor::find($id);
			
			$comunas     = Comuna::where('active',1)->orderBy('name')->get();
			
			return view('proveedors.edit',compact('proveedor','comunas'));
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
			//valida si rut existe
			$validator2 = validator::make($request->all(), [
				'rut' => 'required|string|max:150|unique:proveedors,rut,'.$id,
			]);
			if ($validator2->fails()) {
				return redirect('proveedors/'.$id.'/edit')
							->with('message','rut')
							->withInput();
			} 
						
			// valida otros campos
			$validator = validator::make($request->all(), [
				'name' => 'required|string|max:150|unique:proveedors,name,'.$id,
				'fantasia' => 'nullable|string|max:150',
				'comuna' => 'required',
				'direccion' => 'required|string|max:150',
				'telefono' => 'required|string|max:150',
				'email' => 'nullable|string|max:150',
			]);
			
			if ($validator->fails()) {
				return redirect('proveedors/'.$id.'/edit')
							->withErrors($validator)
							->withInput();
			}
			else {
				$proveedor = Proveedor::find($id);
				
				$proveedor->rut = $request->input('rut');
				$proveedor->dv = $request->input('dv');
				$proveedor->name = $request->input('name');
				$proveedor->fantasia = $request->input('fantasia');
				$proveedor->comuna_id = $request->input('comuna');
				$proveedor->direccion = $request->input('direccion');
				$proveedor->x = $request->input('x');
				$proveedor->y = $request->input('y');
				$proveedor->telefono = $request->input('telefono');
				$proveedor->email = $request->input('email');
				$proveedor->active = $request->input('active');
			
				$proveedor->save();			
				
				return redirect('/proveedors')->with('message','update');
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
