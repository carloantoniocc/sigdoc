<?php

namespace sigdoc\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

use sigdoc\User;
use sigdoc\Role;
use sigdoc\Establecimiento;
use sigdoc\Referente;

use DB;
use Illuminate\Support\Facades\Auth;

use Exception;

/**
 * Clase Controlador Usuarios
 * Rol: Administrador
 */
class UsersController extends Controller
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
	 * Vista: users.index
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
		if (Auth::check()) {
			$users = User::search($request->get('search'))->select('id','name', 'email','active')->orderBy('name')->paginate(10)->appends('search',$request->get('search'));
			return view('users.index',compact('users'));
		}
		else {
			return view('auth/login');
		}
    }

    /**
     * Show the form for creating a new resource.
	 * Vista: users.create
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
		if (Auth::check()) {
			return view('users.create');		
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
				'name'      => 'required|string|max:150',
				'email'     => 'required|string|email|max:150|unique:users',
				'password'  => 'required|string|min:6|confirmed',
				'iniciales' => 'required|string|max:10',
			]);
			
			if ($validator->fails()) {
				return redirect('users/create')
							->withErrors($validator)
							->withInput();
			}
			else {
				$user = new User;
				
				$user->name      = $request->input('name');
				$user->email     = $request->input('email');
				$user->password  = bcrypt($request->input('password'));
				$user->iniciales = $request->input('iniciales');
				$user->active    = $request->input('active');
			
				$user->save();			
				
				return redirect('/users')->with('message','store');
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
	 * Vista: users.edit
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
		if (Auth::check()) {
			$user = User::find($id);
			
			return view('users.edit',compact('user'));
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
				'name'      => 'required|string|max:150',
				'email'     => 'required|string|email|max:150|unique:users,email,'.$id,
				'iniciales' => 'required|string|max:10',
			]);
			
			if ($validator->fails()) {
				return redirect('users/'.$id.'/edit')
							->withErrors($validator)
							->withInput();
			}
			else {
				$user = User::find($id);
				
				$user->name      = $request->input('name');
				$user->email     = $request->input('email');
				$user->iniciales = $request->input('iniciales');
				$user->active    = $request->input('active');
				
				//si cambia el password
				if( $request->input('password') != null ) {
					$validator = validator::make($request->all(), [
						'email'     => 'required|string|email|max:150|unique:users,email,'.$id,
						'password'  => 'required|string|min:6|confirmed',
					]);
					if ($validator->fails()) {
						return redirect('users/'.$id.'/edit')
									->withErrors($validator)
									->withInput();
					}
					else {
						$user->password = bcrypt($request->input('password'));
					}	
				}
			
				$user->save();			
				
				return redirect('/users')->with('message','update');
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
	
	/**
	 * Función que Actualiza ROL
	 * Vista: users.asignRole
	 *
	 * @param  int  $id 
     * @return \Illuminate\Http\Response
	 */
	public function	asignRole($id) {
		if (Auth::check()) {
			$roles     = Role::orderBy('rol')->get();
			$user      = User::find($id);
			
			return view('users.asignRole',compact('user','roles','id'));
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
	 * Función que Guarda ROL
	 *
	 * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
	 */
	public function saveRole(Request $request) {
		if (Auth::check()) {
			$id = $request->input('userID');
			$user = User::find($id);
			
			//Graba nuevos roles asignados
			$roles = $request->input('rolesUsuarios');
			
			$user->roles()->sync($roles);
			
			return redirect('/users')->with('message','roles');
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
	 * Función que Actualiza ESTABLECIMIENTO
	 * Vista: users.asignEstab
	 *
	 * @param  int  $id
     * @return \Illuminate\Http\Response
	 */
	public function	asignEstab($id) {
		if (Auth::check()) {
			$establecimientos = Establecimiento::orderBy('name')->where('active',1)->get();
			$user             = User::find($id);
			
			return view('users.asignEstab',compact('user','establecimientos','id'));
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
	 * Función que Guarda ESTABLECIMIENTO
	 *
	 * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
	 */
	public function saveEstab(Request $request) {
		if (Auth::check()) {
			$id = $request->input('userID');
			$user = User::find($id);
			
			//Graba nuevos roles asignados
			$establecimientos = $request->input('estabUsuarios');
			
			$user->establecimientos()->sync($establecimientos);
			
			return redirect('/users')->with('message','establecimientos');
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
	 * Función que Actualiza REFERENTE
	 * Vista: users.asignRef
	 *
	 * @param  int  $id
     * @return \Illuminate\Http\Response
	 */
	public function	asignRef($id) {
		if (Auth::check()) {
			try {
				$user       = User::find($id);
				//determina el establecimiento del usuario a asignar el departamento (referente)
				$estab_user = $user->establecimientos()->first()->id;
			
				$referentes  = Referente::where([['establecimiento_id',$estab_user],['active',1]])->orderBy('name')->get();
						
				return view('users.asignRef',compact('user','referentes','id'));
			}
			catch(Exception $e) {
				return redirect('/users')->with('message','error_referentes');
			}
		}
		else {
			return view('auth/login');
		}
	}
	
	/**
	 * Función que Guarda REFERENTE
	 *
	 * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
	 */
	public function saveRef(Request $request) {
		if (Auth::check()) {
			$id = $request->input('userID');
			$user = User::find($id);
			
			//Graba nuevos roles asignados
			$referentes = $request->input('refUsuarios');
			
			$user->referentes()->sync($referentes);
			
			return redirect('/users')->with('message','referentes');
		}
		else {
			return view('auth/login');
		}
	}	
}
