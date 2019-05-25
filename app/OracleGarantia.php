<?php

namespace sigdoc;

use Illuminate\Database\Eloquent\Model;

/**
 * Clase Modelo OracleGarantia - Administra datos en Integración con Abex (Documentos de Garantias)
 */
class OracleGarantia extends Model
{
    /**
     * Nombre de Tabla para almacenar información
     *
     * @var string
     */
    protected $table = 'GARANTIAS';
    /**
     * Nombre de Conexión
     *
     * @var string
     */
    protected $connection = 'oracle';
    /**
     * Parametro de Tiempo (Now) seteado Falso
     *
     * @var boolean
     */
    public $timestamps = false;
    /**
     * Parametro de Incrementalidad seteado Falso
     *
     * @var boolean
     */
    public $incrementing = false;
}
