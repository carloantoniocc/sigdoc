<?php

namespace sigdoc\Http\Middleware;

use Closure;

use Auth;

/**
 * Clase Middleware para Perfil de Abastecimiento - Flujo Garantia
 */
class Abastecimiento
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ( Auth::check() && Auth::user()->isRole('Abastecimiento') ) 
		{
			return $next($request);
		}
		
		return redirect('home');
    }
}
