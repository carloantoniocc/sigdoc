<?php

namespace sigdoc\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

use sigdoc\TipoDoc;

use Illuminate\Support\Facades\Auth;

/**
 * Clase Controlador Tipo de Documentos
 * Rol: Administrador
 */
class TipoDocsController extends Controller
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
     * Vista: tipoDocs.index
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
		if (Auth::check()) {
			$tipos = TipoDoc::select('id','name','flujo','oc','active')->orderBy('name')->paginate(10);

			return view('tipoDocs.index',compact('tipos'));
		}
		else {
			return view('auth/login');
		}

    }

    /**
     * Show the form for creating a new resource.
     * Vista: tipoDocs.create
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
		if (Auth::check()) {
			return view('tipoDocs.create');
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
				'name' => 'required|string|max:150|unique:tipo_docs',
				'id_sii' => 'nullable|integer|min:0',
			]);

			if ($validator->fails()) {
				return redirect('tipoDocs/create')
							->withErrors($validator)
							->withInput();
			}
			else {
				$tipoDoc = new TipoDoc;

				$tipoDoc->name 		= $request->input('name');
				$tipoDoc->flujo 	= $request->input('flujo');
				$tipoDoc->oc 		= $request->input('oc');
				$tipoDoc->asociado 	= $request->input('asociado');
				$tipoDoc->id_sii 	= $request->input('id_sii');
				$tipoDoc->resta   	= $request->input('resta');
				$tipoDoc->active	= $request->input('active');

				$tipoDoc->save();

				return redirect('/tipoDocs')->with('message','store');
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
     * Vista: tipoDocs.edit
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
		if (Auth::check()) {
			$tipo = TipoDoc::find($id);

			return view('tipoDocs.edit',compact('tipo'));
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
				'name' => 'required|string|max:150|unique:tipo_docs,name,'.$id,
				'id_sii' => 'nullable|integer|min:0',
			]);

			if ($validator->fails()) {
				return redirect('tipoDocs/'.$id.'/edit')
							->withErrors($validator)
							->withInput();
			}
			else {
				$tipo = TipoDoc::find($id);

				$tipo->name 	= $request->input('name');
				$tipo->flujo 	= $request->input('flujo');
				$tipo->oc 		= $request->input('oc');
				$tipo->asociado = $request->input('asociado');
				$tipo->id_sii 	= $request->input('id_sii');
				$tipo->resta   	= $request->input('resta');
				$tipo->active 	= $request->input('active');

				$tipo->save();

				return redirect('/tipoDocs')->with('message','update');
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
