<?php

namespace sigdoc\Http\Middleware;

use Closure;

use Auth;

/**
 * Clase Middleware para Perfil de Convenios - Flujo Documentos / Garantia
 */
class Convenios
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
        if ( Auth::check() && Auth::user()->isRole('Convenios') ) {
			return $next($request);
		}
      	return redirect('home');
    }
}
