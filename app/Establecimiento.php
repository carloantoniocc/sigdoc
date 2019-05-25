<?php

namespace sigdoc;

use Illuminate\Database\Eloquent\Model;

/**
 * Clase Modelo Establecimiento - Administra datos de Establecimientos
 */
class Establecimiento extends Model
{
    /**
     * Funcion que retorna Comunas Asociados a un Establecimiento
     *
     * @return void clase Comuna
     */
    public function comuna()
    {
        return $this->hasMany('sigdoc\Comuna');
    }
	
    /**
     * Funcion que retorna Tipos de Establecimientos Asociados a un Establecimiento
     *
     * @return void clase Tipo
     */
    public function tipo()
    {
        return $this->hasMany('sigdoc\TipoEstab');
    }
	
    /**
     * Funcion que retorna Usuarios Asociados a un Establecimiento
     *
     * @return void clase Usuarios
     */
	public function users()
    {
        return $this->belongsToMany('sigdoc\User');
    }
	
    /**
     * Funcion que retorna el nombre del establecimiento
     *
     * @param int $id id de Establecimiento
     * @return string nombre de establecimiento
     */
	public function nombreEstablecimiento($id)
	{
		return Establecimiento::select('name')->where('id',$id);
	}
}
