<?php

namespace sigdoc\Http\Middleware;

use Closure;

use Auth;

/**
 * Clase Middleware para Perfil de Oficina de Partes - Flujo Documentos / Garantia
 */
class OficinaPartes
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
        if ( Auth::check() && Auth::user()->isRole('Oficina de Partes') ) {
			return $next($request);
		}
      	return redirect('home');

    }
}
