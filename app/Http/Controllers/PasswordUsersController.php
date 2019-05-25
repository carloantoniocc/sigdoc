<?php

namespace sigdoc\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use sigdoc\User;

use Illuminate\Support\Facades\Auth;

/**
 * Clase Administración de Password de Usuarios
 * Rol: None
 */
class PasswordUsersController extends Controller
{
    /**
     * Llama a vista para el cambio de password.
	 * Vista: users.password
     *
     * @return \Illuminate\Http\Response
     */
    public function password()
    {
		if (Auth::check()) {
			return view('users.password');
		}
		else {
			return view('auth/login');
		}
	}

	/**
     * Guarda nuevo password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
		if (Auth::check()) {
			//valida si rut actual es el mismo
			$current_password = Auth::User()->password; 
			if( Hash::check($request->input('old_password'), $current_password) == false ) {
				return redirect('users/password/cambiar')->with('message','error')->withInput();
			}
			
			// valida si nuevos rut coinciden
			$validator = validator::make($request->all(), [
				'password' => 'required|string|min:6|confirmed',
			]);
			if ($validator->fails()) {
				return redirect('users/password/cambiar')
							->withErrors($validator)
							->withInput();
			}
			
			//guarda nueva contraseña
			$user = User::find(Auth::User()->id);
			$user->password = bcrypt($request->input('password'));
			
			$user->save();			
				
			return redirect('users/password/cambiar')->with('message','store');
			
			
		}
		else {
			return view('auth/login');
		}	
	}		
}
