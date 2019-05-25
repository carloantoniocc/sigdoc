<?php

namespace sigdoc;

use Illuminate\Database\Eloquent\Model;

/**
 * Clase Modelo Proveedor - Administra datos de tipos de Proveedores
 */
class Proveedor extends Model
{
	/**
	 * Funcion para el filtro de proveedores por Nombre
	 *
	 * @param string $query Consulta
	 * @param string $searchNombre paramentro de consulta
	 * @return void
	 */
	public function scopeSearchNombre($query,$searchNombre) {
		if( trim($searchNombre) != "" ){
			$query->where('name', "LIKE", "%$searchNombre%");
		}
	}
	
	/**
	 * Funcion para el filtro de proveedores por Rut
	 *
	 * @param string $query Consulta
	 * @param string $searchRut paramentro de consulta
	 * @return void
	 */
	public function scopeSearchRut($query,$searchRut) {
		if( trim($searchRut) != "" ){
			$query->where('rut', "LIKE", "%$searchRut%");
		}
	}
}
