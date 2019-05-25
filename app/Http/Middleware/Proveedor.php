<?php

namespace sigdoc\Http\Middleware;

use Closure;

use Auth;

/**
 * Clase Middleware para Perfil de Creación de Proveedores - Flujo Documentos / Garantia
 */
class Proveedor
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
        if ( Auth::check() && Auth::user()->isRole('Administrar Proveedores') ) {
			return $next($request);
		}

		return redirect('alertaProveedores');
    }
}
