<?php

/**
 * Interface que debe definirse para todo Transaction Manager
 * de una persistencia , digase base de datos por ejemplo.
 *
 * @author Carlos Arana Reategui
 * @version 1.00 , 29 JUN 2011
 *
 * @since 1.00
 *
 */
interface TSLITransactionManager {

    /**
     * Esta funcion inicializa el transaction manager , en caso que la persistencia esta
     * cerrada procedera a abrirla e inicializarla.
     * Debera enviar una excepcion  de persistencia en caso de error.
     *
     */
    public function init() : void ;

    /**
     * Inicia la transaccion
     * Debera enviar una excepcion  de persistencia en caso de error.
     */
    public function startTransaction() : void ;

    /**
     * Termina la transaccion, esta metodo puede ser llamado en vez
     * de rollback o commit ya que este determina el estado de la transaccion
     * y realiza la operacion que corresponde.
     * Debera enviar una excepcion  de persistencia en caso de error.
     */
    public function endTransaction() : void;

    /**
     * Deshace la transaccion
     * Debera enviar una excepcion  de persistencia en caso de error.
     */
    public function rollback() : void ;

    /**
     * Ejecuta la transaccion
     * Debera enviar una excepcion  de persistencia en caso de error.
     */
    public function commit() : void ;

    /**
     * Cierra la instancia del transaccion manager
     * Debera enviar una excepcion  de persistencia en caso de error.
     */
    public function end() : void ;

    /**
     * Retorna la coneccion a la base de datos sobre la cual se
     * maneja la transaccion abierta por el transaction Manager.
     *
     * @return CI_DB una variable del tipo DB
     */
    public function &getDB() : CI_DB ;

    /**
     * Indica si el transaction manager ya tiene la coneccion a la base de datos abierta.
     *
     * @return bool true si esta ya conectado a la base de datos
     * el transaccion manager.
     */
    public function isAlreadyOpened() : bool ;

    /**
     * Si es true indicara que toda la operacion estara bajo una transaccion
     * de lo contrario cada operacion debera explicitamente
     * delimitar sus transacciones.
     * 
     * @param bool $enable TRUE OR FALSE
     */
    public function enableTransactionMode(bool $enable);

}
