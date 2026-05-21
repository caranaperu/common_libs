<?php

//require_once (APPPATH . '../framework/techsoft/fw/dto/TSLIDataTransferObj.php');

/**
 * Implementacion del Data Transfer Object a ser usado por el framework.
 *
 * @author Carlos Arana Reategui
 * @version 1.00
 *
 * @history 7 JUN 2011 , creacion
 *
 * @since 1.00
 *
 */
class TSLDataTransferObj implements TSLIDataTransferObj {

    /**
     * La lista de modelos a usar,
     * @var array TSLDataModel $m_Models
     */
    private $m_Models = null;

    /**
     * Contiene el mensaje de salida..
     *
     * @var TSLOutMessage $m_OutMessage
     */
    private $m_OutMessage = null;

    /**
     * Contiene los constraints de paginacion si se requirieran
     *
     * @var TSLRequestConstraints
     */
    private $m_Constraints = null;

    /**
     * El usuario de la session
     *
     * @var string
     */
    private $m_sessionUser = null;

    /**
     * El arreglo de parametros que asocia nombre y valor.
     *
     * @var mixed[]
     */
    private $m_Parameters = null;

    /**
     * Se agrega un model el cual sera usado para el proceso del bussiness
     * object.
     *
     * @param string $modelId el identificador unico del modelo a usar
     * @param TSLDataModel $model  la nstancia del modelo
     */
    public function addModel(string $modelId, TSLDataModel &$model) : void  {
        if (isset($modelId)) {
            $this->m_Models[$modelId] = $model;
        }
    }

    /**
     * Retorna la instancia de modelo basado en el id enviado,
     * de no existir retorna un objeto indefinido.
     *
     * @param string $modelId con el identificador unico de un modelo de trabajo.
     * @return TSLDataModel|null la instancia del modelo.
     */
    public function &getModel(string $modelId) : ?TSLDataModel {
        // Retorna el modelo asociado al model Id , null
        // si no existe.
        return $this->m_Models[$modelId];
    }

    /**
     *
     * @inheritdoc
     */
    public function addParameter(string $parameterId, $parameterData) : void {
        if (isset($parameterId)) {
            $this->m_Parameters[$parameterId] = $parameterData;
        }
    }

    /**
     *
     * @inheritdoc
     */
    public function getParameterValue(string $parameterId) {
        if (isset($this->m_Parameters[$parameterId]))
            return $this->m_Parameters[$parameterId];
        else
            return NULL;
    }

    /**
     * Retorna la instancia del objeto de salida.
     * @return TSLOutMessage la intancia del mensaje de salida.
     *
     * @inheritdoc
     */
    public function &getOutMessage() : TSLOutMessage {
        // Si no esta creado lo creamos y retornamos el nuevo objeto,
        // de lo contrario retornamos la instancia existente.
        if (!isset($this->m_OutMessage) || is_null($this->m_OutMessage)) {
            $this->m_OutMessage = new TSLOutMessage();
            // Por default en false
            $this->m_OutMessage->setSuccess(false);
        }
        return $this->m_OutMessage;
    }

    /**
     *
     * Retorna la instancia de los constraints para ser seteada en otras
     * capas del framework.
     *
     * @return TSLRequestConstraints la intancia delos constraints.
     *
     */
    public function &getConstraints() : TSLRequestConstraints {
        // Si no esta creado lo creamos y retornamos el nuevo objeto,
        // de lo contrario retornamos la instancia existente.
        if (!isset($this->m_Constraints) || is_null($this->m_Constraints))
            $this->m_Constraints = new TSLRequestConstraints ();
        return $this->m_Constraints;
    }

    /**
     * @inheritdoc
     */
    public function setOperation(string $operation) : void {
        $this->addParameter("operation", $operation);
    }

    /**
     * @inheritdoc
     */
    public function getOperation() : string {
        return $this->getParameterValue("operation");
    }

    /**
     *
     * @inheritdoc
     */
    public function getSessionUser() : string  {
        return $this->m_sessionUser;
    }

    /**
     * @inheritdoc
     */
    public function setSessionUser(string $m_sessionUser) : void {
        $this->m_sessionUser = $m_sessionUser;
    }

    /**
     * @inheritdoc
     */
    public function setSubOperationId(string $suboperation) : void {
        $this->addParameter("suboperation", $suboperation);
    }

    /**
     * @inheritdoc
     */
    public function getSubOperation() : ?string {
        return $this->getParameterValue("suboperation");
    }

}