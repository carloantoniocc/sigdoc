<?php

namespace sigdoc\Http\Middleware;

use Closure;

use Auth;

/**
 * Clase Middleware para Perfil de Refernete Técnico - Flujo Documentos
 */
class ReferenteTecnico
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
        if ( Auth::check() && Auth::user()->isRole('Referente Tecnico') ) {
			return $next($request);
		}
      	return redirect('home');
    }
}
