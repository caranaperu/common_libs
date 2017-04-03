<?php

/**
 * Interface que debe definirse para todo Data Transfer Object, el mismo
 * que es usado como objeto goma entre las diferentes capas del sistema.
 *
 *
 * @author Carlos Arana Reategui
 * @version 1.00 , 27 MAY 2011
 *
 * @since 1.00
 *
 */
interface TSLIDataTransferObj {
    // Constantes basicas para las operaciones soportadas , pueden usarse otras
    // pero estas seran de interpretacion para la clase que implemeta , solo estas
    // se encuentran directamente soportadas en el framework.

    const OP_FETCH  = 'fetch';
    const OP_READ   = 'read';
    const OP_UPDATE = 'upd';
    const OP_DELETE = 'del';
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
     * Setea el modelo de datos , habitualmente de entrada a procesar.
     *
     * @param string       $modelId
     * @param TSLDataModel $model , referencia al  modelo de datos a procesar.
     */
    public function addModel(string $modelId, TSLDataModel &$model): void;

    /**
     * Retorna el modelo identificado por $modelId
     *
     * @param string $modelId
     *
     * @return TSLDataModel el cual es identificado por $modelId o
     * null si no existe.
     */
    public function &getModel(string $modelId): TSLDataModel;

    /**
     * Retorna el mensaje de salida..
     *
     * @return TSLOutMessage conteniendo la data de respuesta con errores
     * o data , dependiendo del tipo de retorno , osea con o sin error..
     */
    public function &getOutMessage(): TSLOutMessage;

    /**
     * Retorna el onjeto de constraints para su llenado.
     *
     * @return TSLRequestConstraints conteniendo los datos de entrada para ser
     * usadops como constraints en la capa de datos.
     */
    public function &getConstraints(): TSLRequestConstraints;

    /**
     * Setea el tipo de operacion a realizar por el request , estas
     * pueden ser : 'add','del','fetch','upd' , cualquier este valor
     * por ahora no sera interpretado adecuadamente por la libreria
     * pero podria ser usado por implementaciones propias.
     *
     * @param string $operation Tipo de operacion a realizar por el request
     */
    public function setOperation(string $operation): void;

    /**
     * Retorna el tio de operacion a efectua . se deja la interpretacion
     * a la clase que requiera estaa informacion.
     * @return string con el tipo de operacion a efectuar
     */
    public function getOperation(): string;


    /**
     * Setea el subtipo de operacion a realizar por el request , digamo
     * que existen diversas formas de hacer un fetch , aqui podria indicarse
     * 'fetchAll','fetchOnlyassociated' por ejemplo , de tal manera
     * que el bussiness object pueda determinar si una operacion tiene
     * alguna sub opcion a ejecutar.
     *
     * @param string $suboperation subtipo de operacion a realizar por el request
     */
    public function setSubOperationId(string $suboperation): void;

    /**
     * Retorna el tio de suboperacion a efectua . se deja la interpretacion
     * a la clase que requiera estaa informacion.
     *
     * @return string | null con el tipo de suboperacion a efectuar
     */
    public function getSubOperation(): ?string;


    /**
     *
     * @return string con el usuario de la sesion
     */
    public function getSessionUser(): string;

    /**
     * Retorna el nombre del usuario de la sesion.
     *
     * @param string $m_sessionUser
     */
    public function setSessionUser(string $m_sessionUser): void;
}

