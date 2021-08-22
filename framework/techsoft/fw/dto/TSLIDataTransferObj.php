<?php

/**
 * Interface que debe definirse para todo Data Transfer Object, el mismo
 * que es usado como objeto goma entre las diferentes capas del sistema.
 *
 * En este objeto deberan ponerse los parametros externos para que sea procesado en las
 * capas posteriores y asi mismo las capas posteriores deberan poner los datos de salida en el mismo,
 * sea que sea un mensaje de error o los datos obtenidos.
 *
 * @author Carlos Arana Reategui
 * @version 1.00 , 27 MAY 2011
 *
 * @since 1.00
 *
 */
interface TSLIDataTransferObj {
    /***
     * @var string OP_FETCH Representa la operacion de fetch o query a la persistencia
     */
    const OP_FETCH  = 'fetch';
    /***
     * @var string OP_READ Representa la operacion de lectura a la persistencia
     */
    const OP_READ   = 'read';
    /***
     * @var string OP_UPDATE Representa la operacion de actualizacion a la persistencia
     */
    const OP_UPDATE = 'upd';
    /***
     * @var string OP_DELETE Representa la operacion de eliminacion a la persistencia
     */
    const OP_DELETE = 'del';
    /***
     * @var string OP_ADD Representa la operacion de agregar a la persistencia
     */
    const OP_ADD    = 'add';

    /**
     * Agrega parametros a usarse durante el desarrollo del bussines object.
     *
     * @param String $parameterId que identifica al parametro , por ejemplo "orderby"
     * @param Mixed  $parameterData que identifica el valor del parametro , pj "username"
     */
    public function addParameter(string $parameterId, $parameterData): void;

    /**
     * Retorna el valor del parametro identificado por
     * $parameterId
     *
     * @param String $parameterId con el valor que idetifica al parametro
     *
     * @return Mixed el objeto o valor del parametro.
     */
    public function getParameterValue(string $parameterId);

    /**
     * Agrega un modelo de datos a trabajar identificado por $modeloId,
     *
     * @param string       $modelId Identificador unico para este modelo
     * @param TSLDataModel $model  referencia al  modelo de datos a procesar.
     */
    public function addModel(string $modelId, TSLDataModel &$model): void;

    /**
     * Retorna la referencia al modelo identificado por $modelId
     *
     * @param string $modelId el identificador unico del modelo a buscar
     *
     * @return TSLDataModel|null el cual es identificado por $modelId o
     * null si no existe.
     */
    public function &getModel(string $modelId): ?TSLDataModel;

    /**
     * Retorna la referencia al mensaje de salida.
     **
     * @return TSLOutMessage conteniendo la data de respuesta con errores
     * o data , dependiendo del tipo de retorno , osea con o sin error..
     */
    public function &getOutMessage(): TSLOutMessage;

    /**
     * Retorna la referencia el objeto de constraints para su llenado.
     *
     * @return TSLRequestConstraints conteniendo los datos de entrada para ser
     * usadops como constraints en la capa de datos.
     */
    public function &getConstraints(): TSLRequestConstraints;

    /**
     * Setea el tipo de operacion a realizar por el request , estas
     * pueden ser : 'add','del','fetch','upd' , cualquier otro valor
     * por ahora no sera interpretado adecuadamente por la libreria
     * pero podria ser usado por implementaciones propias.
     *
     * Por conveniencia usar las constantes de esta interfase :
     * <pre>
     * TSLIDataTransferObj::OP_FETCH
     * TSLIDataTransferObj::OP_ADD
     * TSLIDataTransferObj::OP_DELETE
     * TSLIDataTransferObj::OP_UPDATE
     * TSLIDataTransferObj::OP_READ
     * <pre>
     *
     * @param string $operation Tipo de operacion a realizar por el request
     */
    public function setOperation(string $operation): void;

    /**
     * Retorna el tipo de operacion a efectuar. se deja la interpretacion
     * a la clase que requiera estaa informacion.
     *
     * @return string con el tipo de operacion a efectuar
     */
    public function getOperation(): string;


    /**
     * Setea el subtipo de operacion a realizar por el request , digamos
     * que existen diversas formas de hacer un fetch , aqui podria indicarse
     * 'fetchAll','fetchOnlyassociated' por ejemplo , de tal manera
     * que el bussiness object pueda determinar si una operacion tiene
     * alguna sub opcion a ejecutar.
     *
     * @param string $suboperation subtipo de operacion a realizar por el request
     */
    public function setSubOperationId(string $suboperation): void;

    /**
     * Retorna el tipo de suboperacion a efectua . se deja la interpretacion
     * a la clase que requiera esta informacion.
     *
     * @return string|null con el tipo de suboperacion a efectuar
     */
    public function getSubOperation(): ?string;


    /**
     * Retorna el nombre del usuario de la sesion.
     *
     * @return string con el usuario de la sesion
     */
    public function getSessionUser(): string;

    /**
     * Setea el nombre del usuario de la sesion.
     *
     * @param string $m_sessionUser
     */
    public function setSessionUser(string $m_sessionUser): void;
}

