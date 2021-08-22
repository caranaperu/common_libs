<?php

/**
 * Clase abstract que implementa la logica de un Servicio de Negocio, es abstracta ya que requiere
 * implementar metodos especificos a cada clase que la implemente.
 *
 * @author carana
 * @version 1.00
 * @since 1.0
 *
 */
abstract class TSLBussinessService implements TSLIBussinessService {

    /**
     * @inheritdoc
     */
    public function executeService(string $action, TSLIDataTransferObj $dto) : void {
        // La implementacion default efectua las acciones de validar , dar la oportunidad de
        // hacer operaciones antes y despues de ejecutar el servicio.
        if ($this->validateData($dto) === TRUE) {
            $this->preExecuteService($action, $dto);
            $this->doService($action, $dto);
            $this->postExecuteService($action, $dto);
        }
    }

    /**
     * Sera llamada para tener la oportunidad de validar los datos del DTO
     * en caso fuera necesario.
     *
     * @param TSLIDataTransferObj $dto el Data Transfer Object conteniendo los datos
     * a validar y luego procesar.
     *
     * @return bool true si es correcta la validacion , false de lo contrario
     */
    abstract protected function validateData(TSLIDataTransferObj $dto) : bool;

    /**
     * Si se requiere efectuar un proceso previo sobre los datos del DTO , override de este
     * metodo permitira efectuar este trabajo de acuerdo a los requerimientos especificos.
     *
     * @param string              $action string que contendra la accion a efectuar
     * @param TSLIDataTransferObj $dto el Data Transfer Object conteniendo los datos
     * a procesar.
     */
    abstract protected function preExecuteService(string $action,TSLIDataTransferObj $dto) : void ;

    /**
     * Este metodo efectuara la accion requerido al Bussiness Object dependiendo de la accion
     * solicitada , override de este metodo permitira efectuar acciones especificas.
     *
     * @param string              $action string que contendra la accion a efectuar
     * @param TSLIDataTransferObj $dto el Data Transfer Object conteniendo los datos
     * a procesar.
     */
    abstract protected function doService(string $action,TSLIDataTransferObj $dto) : void ;

    /**
     * Si se requiere efectuar un proceso posterior a la ejecucion del servicio sobre los datos del DTO ,
     * override de este  metodo permitira efectuar este trabajo de acuerdo a los requerimientos
     * especificos.
     *
     * @param string              $action string que contendra la accion a efectuar
     * @param TSLIDataTransferObj $dto el Data Transfer Object conteniendo los datos
     * a procesar.
     */
    abstract protected function postExecuteService(string $action,TSLIDataTransferObj $dto) : void ;

}

