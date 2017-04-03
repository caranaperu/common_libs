<?php
/**
 * Clase abstract que implementa la logica de un Servicio
 * de Negocio.
 *
 * @author carana
 */
abstract class TSLBussinessService implements TSLIBussinessService {

    public function executeService($action, TSLIDataTransferObj $dto) : void {
        if ($this->validateData($dto) === TRUE) {
            $this->preExecuteService($action, $dto);
            $this->doService($action, $dto);
            $this->postExecuteService($action, $dto);
        }
    }

    abstract protected function validateData(TSLIDataTransferObj $dto) : bool;
    abstract protected function preExecuteService(string $action,TSLIDataTransferObj $dto) : void ;
    abstract protected function doService(string $action,TSLIDataTransferObj $dto) : void ;
    abstract protected function postExecuteService(string $action,TSLIDataTransferObj $dto) : void ;

}

