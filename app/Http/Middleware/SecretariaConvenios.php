<?php

namespace sigdoc\Http\Middleware;

use Closure;

use Auth;

/**
 * Clase Middleware para Perfil de Secretaria de Convenios - Flujo Documentos
 */
class SecretariaConvenios
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
        if ( Auth::check() && Auth::user()->isRole('Secretaria de Convenios') ) {
			return $next($request);
		}
      	return redirect('home');
    }
}
