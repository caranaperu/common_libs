<?php

namespace app\common\controller;

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Controlador base default para las aplicaciones.
 * Implementa lo minimo indispensable para que el controlador funcione , tales como
 * metodo para que parametros que llegan con 'null' sean convertidos a PHP NULL, define
 * un default views TSLDefaultDataResponseView que es usado para casos que simplemente se
 * requiere retornar datos y no vistas reales (visuales) y basicamente implementa la carga
 * default para el response processor y el constraint processor, entre otros.
 * Adicionalmente el default para los metodos de la interface TSLISessionController.
 *
 * Los reponse y constraint processor seran cargados dinamicamente a partir de los parametros
 * libid (que libreria se esta usando) y filterformat (XML,json,etc)
 *
 * @author  $Author: aranape $
 * @since   06-FEB-2013
 * @version 1.01
 * @history 01-08-2020 getUserCode puede retornar null
 *          11/08/2020 Mejora en la documentacion.
 *
 * @see TSLDefaultDataResponseView
 * @see TSLISessionController
 *
 * @todo Las nombres de las variables de session deben ser seteables.
 *
 */
class TSLAppDefaultController extends \TSLBaseController implements \TSLISessionController, \TSLIConstrainedController {

    /**
     * @var bool si es true no se validara el login , esto es util cuando el controlador
     * es llamado pro un proceso interno automatico o algun caso similar.
     */
    protected $relaxLoginCheck = false;

    public function __construct() {
        parent::__construct();
        // Dos posibilidades
        // 1: dentro del apppath para adentro
        // 2: a la altura de apppath para abajo
        //$this->load->add_package_path(APPPATH.'framework/techsoft/fw/');
        $this->load->add_package_path(BASEPATH.'../framework/techsoft/fw/');
    }

    /**
     * Funcion de apoyo para eliminar los parametros
     * que llegan con el valor 'null' en texto ya que
     * sera asumido como texto y no un real NULL en la capa
     * de persistencia , de tal forma que en esos casos se asignara
     * un real NULL al parametro, previo a su proceso posterior.
     *
     * En el caso $beginWith fuera un array entonces se chequeara para
     * cada caso en el arreglo , esto es util cuando los parametros
     * de entrada a chequear no tienen un solo prefijo.
     *
     * @param string | array $beginWith Para los parametros que inician con
     * este valor.
     */
    protected function parseParameters($beginWith) : void {

        if (is_array($beginWith)) {
            foreach ($beginWith as $str) {
                // Parche de parametros
                foreach ($_POST as $i => $value) {
                    // Comienza con?
                    if (strpos($i, $str, 0) === 0) {
                        $this->fixParameter($i, 'null', NULL);
                    }
                }
            }
        } else {
            // Parche de parametros
            foreach ($_POST as $i => $value) {
                // Comienza con?
                if (strpos($i, $beginWith, 0) === 0) {
                    $this->fixParameter($i, 'null', NULL);
                }
            }
        }
    }

    /**
     * Retorna por defecto TSLDefaultDataResponseView , que es una vista que solo coloca
     * la data como salida,generalmente se usa como salida para los AJAX calls.
     * Puede hacerse override para cambiar esto.
     *
     */
    public function getView() : string  {
        return 'TSLDefaultDataResponseView';
    }

    /**
     * Cargara por default el response processor que el usuario haya definido para el tipo
     * que se indique y de no indicarse el tipo buscara el default que para este caso es el de JSON.
     *
     * El usuario de la clase debera definir el metodo getUserResponseProcessor para indicar el
     * response processor a usar y asi mismo indicar el tipo de processor a traves de getDefaultFilterType
     *
     * Es importante indicar que si en el REQUEST viene indicado el parametro "filterformat" este sera tomado
     * sobre el indicado por getDefaultFilterType.
     *
     * @throws \TSLProgrammingException
     * @see TSLAppDefaultController::getUserResponseProcessor()
     *
     * @see TSLAppDefaultController::getDefaultFilterType()
     */
    public function getResponseProcessor() : \TSLIResponseProcessor {
        return \TSLResponseProcessorLoaderHelper::loadProcessor($this->getUserResponseProcessor(), isset($_REQUEST['filterformat']) ? $_REQUEST['filterformat'] : $this->getDefaultFilterType(), isset($_REQUEST['libid']) ? $_REQUEST['libid'] : NULL);
    }

    /*     * ************************************************************************
     * De la interace para constrained controller
     */

    /**
     * @return string por default este controlador define JSON.
     */
    public function getDefaultFilterType() : string {
        return 'json';
    }

    /**
     * @return string por default este controlador define JSON.
     */
    public function getDefaultSorterType() : string {
        return 'json';
    }

    public function getFilterProcessor() : ?\TSLIInputProcessor{
        return NULL;
    }

    public function getSorterProcessor() : ?\TSLIInputProcessor {
        return NULL;
    }

    /**
     * Cargara por default el constraint processor que el usuario haya definido para el tipo
     * que se indique y de no indicarse el tipo buscara el default que para este caso es el de JSON.
     *
     * El usuario de la clase debera definir el metodo getFilterProcessor para indicar el
     * constraint processor a usar y asi mismo indicar el tipo de processor a traves de getDefaultFilterType
     *
     * Es importante indicar que si en el REQUEST viene indicado el parametro "filterformat" este sera tomado
     * sobre el indicado por getDefaultFilterType.
     *
     * @throws \TSLProgrammingException
     * @see TSLAppDefaultController::getUserResponseProcessor()
     *
     * @see TSLAppDefaultController::getFilterProcessor()
     */
    public function getConstraintProcessor() : \TSLIInputProcessor {
        return \TSLConstraintProcessorLoaderHelper::loadProcessor($this->getFilterProcessor(), isset($_REQUEST['filterformat']) ? $_REQUEST['filterformat'] : $this->getDefaultFilterType(), isset($_REQUEST['libid']) ? $_REQUEST['libid'] : NULL);
    }

    // De la interface de session \TSLISessionController
    //
    //

    /**
     * La implementacion default buscara en la sesion la llave
     * "usuario_code".
     *
     */
    public function getUserCode() : ?string {
        if ($this->session->userdata('usuario_code') !== FALSE) {
            return $this->session->userdata('usuario_code');
        } else {
            return null;
        }
    }

    /**
     * La implementacion default buscara en la sesion la llave
     * "usuario_id".
     */
    public function getUserId() : int {
        if ($this->session->userdata('usuario_id') !== FALSE) {
            return $this->session->userdata('usuario_id');
        } else {
            return -1;
        }
    }

    /**
     * La implementacion default buscara en la sesion la llave
     * "isLoggedIn" y de existir indicara que el usuario esta logeado.
     *
     */
    public function isLoggedIn() : bool {
        if ($this->relaxLoginCheck == false) {
            if ($this->session->userdata('isLoggedIn') !== FALSE) {
                return $this->session->userdata('isLoggedIn') ?? false;
            } else {
                return false;
            }
        } else {
            // fake login
            return true;
        }
    }

    /**
     * @inheritDoc
     */
    public function getSessionData(string $name) {
        return $this->session->userdata($name);
    }

    /**
     * @inheritDoc
     */
    public function setUserCode(string $userCode) : void {
        $this->session->set_userdata('usuario_code',$userCode);
    }

    /**
     * @inheritDoc
     */
    public function setUserId(int $userId) : void {
        $this->session->set_userdata('usuario_id',$userId);
    }

    /**
     * @inheritDoc
     */
    public function setLoggedIn(bool $isLoggedIn) : void {
        $this->session->set_userdata('isLoggedIn',$isLoggedIn);
    }

    /**
     * @inheritDoc
     */
    public function setSessionData(string $name, $data) : void {
        $this->session->set_userdata($name,$data);
    }

    /**
     * @inheritDoc
     */
    public function unsetSessionData(string $name) : void {
        $this->session->unset_userdata($name);
    }


}
