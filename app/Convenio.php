<?php

namespace sigdoc;

use Illuminate\Database\Eloquent\Model;

/**
 * Clase Modelo Convenio - Administra datos de Convenios de Proveedores
 */
class Convenio extends Model
{
    /**
     * Funcion que retorna Validadores Asociados a Convenios
     *
     * @return void clase Validadores
     */
	public function validadores()
    {
        return $this->belongsToMany('sigdoc\Validador');
    }
    
    /**
     * Funcion que verifica si Validador se encuentra asociado a Convenio
     *
     * @param string $validadorName Nombre del Validador
     * @return boolean True - Si Validador está relacionado al Convenio. Sino, Falso.
     */
	public function isValidador($validadorName)
    {
        foreach ($this->validadores()->get() as $validador)
        {
			if ($validador->name == $validadorName)
            {
                return true;
            }
        }

        return false;
    }
}
