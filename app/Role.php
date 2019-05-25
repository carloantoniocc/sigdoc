<?php

namespace sigdoc;

use Illuminate\Database\Eloquent\Model;

/**
 * Clase Modelo Role - Administra datos de Roles de Usuario (sin mantenedor)
 */
class Role extends Model
{
    /**
     * Funcion que retorna Usuarios Asociados a un Rol
     *
     * @return void clase Usuario
     */
     public function users()
    {
        return $this->belongsToMany('sigdoc\User');
    }
}
