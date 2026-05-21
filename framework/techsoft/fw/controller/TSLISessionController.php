<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Interfase base para controladores que manejen requerimientos de
 * session.
 *
 * @author $Author: aranape $
 * @since 17-May-2012
 * @version $Id: TSLISessionController.php 4 2014-02-11 03:31:42Z aranape $
 * @history 01-08-2020 getUserCode puede retornar null
 *
 * $Date: 2014-02-10 22:31:42 -0500 (lun, 10 feb 2014) $
 * $Rev: 4 $
 */
interface TSLISessionController  {


    /**
     * Metodo hook que se usara para determinar el usuario conectado a la session
     * debe retornar null si no hay ninguno conectado.
     *
     * @return string con el codigo del usuario conectado a la session o null de no
     * existir ninguno.
     *
     */
    public function getUserCode() : ?string ;

    /**
     * Retorna el id del usuario logeado , debe ser -1
     * si no existe ninguno.
     *
     * @return int con el id del usuario logeado o -1 de no haber alguno.
     */
    public function getUserId() : int ;

    /**
     * Retorna si el usuariop esta conectado al sistema.
     *
     * @return bool true si el usuario esta logeado al sistema.
     */
    public function isLoggedIn() : bool ;

    /**
     * Retorna el valor de un dato de sesion en base a una llave.
     *
     * @param string $name con el nombre llave del dato a buscar en la sesion.
     *
     * @return mixed  retorna el datro guardado en la sesion
     */
    public function getSessionData(string $name);

    /**
     * Setea el codigo del usuario logeado , este valor tendra sentido
     * ser seteado si isLoggedIn esta seteado.
     *
     * @param string $userCode con el codigo del usuario a logearse.
     */
    public function setUserCode(string $userCode) : void ;

    /**
     * Setea el id del usuario logeado , este valor tendra sentido
     * ser seteado si isLoggedIn esta seteado.
     *
     * @param int $userId con el id del usario logeado.
     *
     */
    public function setUserId(int $userId) : void ;

    /**
     * Setea si el usuario esta logeado o no al sistema.
     *
     * @param bool $isLoggedIn true si el usuario esta logeado.
     *
     */
    public function setLoggedIn(bool $isLoggedIn) : void ;

    /**
     * Guarda un valor en la sesion.
     *
     * @param string $name con la llave del dato a agregar a la sesion.
     * @param mixed $data el valor a guardar en la sesion
     */
    public function setSessionData(string $name,$data) : void ;

    /**
     * Remueve un valor en la sesion.
     *
     * @param $name string con la llave del dato a remover a la sesion.
     */
    public function unsetSessionData(string $name) : void ;

}


