<?php

namespace app\common\model\impl;

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Modelo para los perfiles de los sistemas , este modelo representa
 * en realidad el header del perfil.
 *
 * @author $Author: aranape $
 * @history  11-02-2017 Compatibilidad con php 7 .
 */
class TSLAppPerfilModel extends \TSLDataModel {

    protected $sys_systemcode;
    protected $perfil_codigo;
    protected $perfil_descripcion;
    protected $perfil_id;

    public function get_perfil_id() : int {
        return $this->perfil_id;
    }

    public function set_perfil_id(int $perfil_id) : void {
        $this->perfil_id = $perfil_id;
        $this->setId($perfil_id);
    }

    /**
     * Para soportar una sola tabla para muultiples sistemas , cada entrada
     * de perfil (header) debe infdicar a que sistema pertene.
     *
     * @param string $sys_systemcode con el identificador unico del sistema
     */
    public function set_sys_systemcode(string $sys_systemcode) : void {
        $this->sys_systemcode = $sys_systemcode;
    }

    /**
     *
     * @return string con el identificador de sistema
     */
    public function get_sys_systemcode() : string {
        return $this->sys_systemcode;
    }

    /**
     * Retorna el codigo que identifica a perfil
     *
     * @return String con el codigo.
     */
    public function get_perfil_codigo() : string {
        return $this->perfil_codigo;
    }

    /**
     * Setea el codigo que identifica el perfil
     * en este caso es tambien el unique key o id.
     * @param String $perfil_codigo con el codigo del perfil.
     */
    public function set_perfil_codigo(string $perfil_codigo) : void {
        // El codigo es tratado como id.
        $this->perfil_codigo = $perfil_codigo;
    }

    public function get_perfil_descripcion() : string {
        return $this->perfil_descripcion;
    }

    public function set_perfil_descripcion(string $perfil_descripcion) : void {
        $this->perfil_descripcion = $perfil_descripcion;
    }

    public function &getPKAsArray() : array {
        $pk['perfil_id'] = $this->getId();
        return $pk;
    }

    public function isPKSequenceOrIdentity() : bool {
        return true;
    }

}

?>