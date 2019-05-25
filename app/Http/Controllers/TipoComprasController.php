<?php

namespace sigdoc\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

use sigdoc\TipoCompra;

use Illuminate\Support\Facades\Auth;

/**
 * Clase Controlador Tipo de Compras
 * Rol: Administrador
 */
class TipoComprasController extends Controller
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
     * Vista: tipoCompras.index
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
		if (Auth::check()) {
			$tipos = TipoCompra::select('id','name','active')->orderBy('name')->paginate(10);
			
			return view('tipoCompras.index',compact('tipos'));
		}
		else {
			return view('auth/login');
		}		
    }

    /**
     * Show the form for creating a new resource.
     * Vista: tipoCompras.create
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
		if (Auth::check()) {
			return view('tipoCompras.create');		
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
				'name' => 'required|string|max:150|unique:tipo_compras',
			]);
			
			if ($validator->fails()) {
				return redirect('tipoCompras/create')
							->withErrors($validator)
							->withInput();
			}
			else {
				$tipoCompra = new TipoCompra;
				
				$tipoCompra->name = $request->input('name');
				$tipoCompra->active = $request->input('active');
			
				$tipoCompra->save();			
				
				return redirect('/tipoCompras')->with('message','store');
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
     * Vista: tipoCompras.edit
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
		if (Auth::check()) {
			$tipo = TipoCompra::find($id);
			
			return view('tipoCompras.edit',compact('tipo'));
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
				'name' => 'required|string|max:150|unique:tipo_compras,name,'.$id,
			]);
			
			if ($validator->fails()) {
				return redirect('tipoCompras/'.$id.'/edit')
							->withErrors($validator)
							->withInput();
			}
			else {
				$tipo = TipoCompra::find($id);
				
				$tipo->name = $request->input('name');
				$tipo->active = $request->input('active');
			
				$tipo->save();			
				
				return redirect('/tipoCompras')->with('message','update');
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
