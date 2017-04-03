<?php
/**
 * Define la implementacion default de un Bussiness Service
 * para los metodos menos usados.
 *
 *
 * @author Carlos Arana Reategui
 * @version 1.00 , 03 AGO 2011
 *
 * @since 1.00
 */
abstract class TSLStandardBussinessService extends TSLBussinessService {

     protected function validateData(TSLIDataTransferObj $dto) : bool {
         return TRUE;
     }

     protected function preExecuteService(string $action,TSLIDataTransferObj $dto) : void {
     }

     protected function postExecuteService(string $action,TSLIDataTransferObj $dto) : void {
     }

}

