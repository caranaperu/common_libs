<?php
/**
 * Define la implementacion default de un Bussiness Service para los metodos menos usados.
 *
 * Esta clase es un comodity para el caso que no se requiera validacion y asi no sea necesario
 * implementar este metodo. (Para los casos WEB esto generalmente se hace en el controller ni bien llegan
 * los parametros externos via REQUEST).
 * Asi mismo preExecuteService y postExecuteService por default no efectuan accion alguna.
 *
 *
 * @author Carlos Arana Reategui
 * @version 1.00 , 03 AGO 2011
 *
 * @since 1.00
 */
abstract class TSLStandardBussinessService extends TSLBussinessService {

    /**
     * Para este caso siempre retrornara TRUE y no sera necesario un override
     * si la validacion no se dara en el bussinesss object.
     *
     * @inheritDoc
     *
     */
     protected function validateData(TSLIDataTransferObj $dto) : bool {
         return TRUE;
     }

    /**
     * Para este caso el override no efectua accion alguna.
     *
     * @inheritDoc
     *
     */
     protected function preExecuteService(string $action,TSLIDataTransferObj $dto) : void {
     }

    /**
     * Para este caso el override no efectua accion alguna.
     *
     * @inheritDoc
     *
     */
     protected function postExecuteService(string $action,TSLIDataTransferObj $dto) : void {
     }

}

