<?php

namespace app\common\model\impl;

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Modelo para los items de detalle del perfil
 *
 * @author $Author: aranape $
 * @history  11-02-2017 Compatibilidad con php 7 , manejo del booleano se tuvo que ajustar.
 */
class TSLAppPerfilDetalleModel extends \TSLDataModel {

    protected $perfdet_id;
    protected $perfil_id;
    protected $perfdet_accessdef;
    protected $perfdet_accleer;
    protected $perfdet_accagregar;
    protected $perfdet_accactualizar;
    protected $perfdet_acceliminar;
    protected $perfdet_accimprimir;
    protected $menu_id;

    /**
     *
     * @return int el id unico de esta linea de detalle del perfil
     */
    public function get_perfdet_id() : int {
        return $this->perfdet_id;
    }

    /**
     * Setea el id unico de esta linea de detalle del perfil
     *
     * @param int $perfdet_id el id unico de esta linea de detalle del perfil
     */
    public function set_perfdet_id(int $perfdet_id) : void {
        $this->perfdet_id = $perfdet_id;
        $this->setId($perfdet_id);
    }

    /**
     * Lista de accesos permitiods a este menu :
     * 'A' - Accesable
     * 'C' - Agregar o Crear registros
     * 'R' - Leer registros
     * 'U' - Actualizar registros
     * 'D' - Eliminar registros
     * 'P' - Imprimir
     *
     * @return string con la lista de accesos permitidos
     */
    public function get_perfdet_accessdef() : string {
        return $this->perfdet_accessdef;
    }

    /**
     * Setea un string con la lista de accesos, se uniran
     * por ejemplo 'CRUD' , la 'A' debera siempre estar definida de lo
     * contrario se ignorara lo demas ya que la 'A' indica acceso.
     *
     * Lista de accesos permitiods a este menu :
     * 'A' - Accesable
     * 'C' - Agregar o Crear registros
     * 'R' - Leer registros
     * 'U' - Actualizar registros
     * 'D' - Eliminar registros
     * 'P' - Imprimir
     *
     * @param string $perfdet_accessdef
     */
    public function set_perfdet_accessdef(string $perfdet_accessdef) : void {
        if (!isset($perfdet_accessdef) || $perfdet_accessdef = null) {
            $this->perfdet_accessdef = 'A';
        } else {
            $this->perfdet_accessdef = $perfdet_accessdef;
        }
    }

    /**
     * Retorna el id unico que identifica a perfil
     * en este caso es el foreign key a la cabecera del perfil
     *
     * @return integer con el id.
     */
    public function get_perfil_id() : int {
        return $this->perfil_id;
    }

    /**
     * Setea el id unico que identifica el perfil
     * en este caso es el foreign key a la cabecera del perfil
     *
     * @param int $perfil_id con el codigo del perfil.
     */
    public function set_perfil_id(int $perfil_id) : void {
        $this->perfil_id = $perfil_id;
    }

    /**
     * Retorna la fk al menu al cual da o quita acceso esta entrada al perfil
     *
     * @return int fk al menu que representa esta entrada del derfil
     */
    public function get_menu_id() : int {
        return $this->menu_id;
    }

    /**
     * Setea la fk al menu al cual da o quita acceso esta entrada al perfil
     *
     * @param int $menu_id
     */
    public function set_menu_id(int $menu_id) : void {
        $this->menu_id = $menu_id;
    }

    /*     * *******************************************************************
     *      PERMISOS
     *
     * ******************************************************************** */

    /**
     *
     * @return boolean true acceso de lectura
     * false denegado
     */
    public function get_perfdet_accleer() : bool {
        return $this->perfdet_accleer;
    }

    /**
     *
     * @return boolean true acceso para agregar registros
     *  false denegado
     */
    public function get_perfdet_accagregar() : bool {
        return $this->perfdet_accagregar;
    }

    /**
     *
     * @return boolean true acceso para actualizar registros
     *  false denegado
     */
    public function get_perfdet_accactualizar() : bool {
        return $this->perfdet_accactualizar;
    }

    /**
     *
     * @return boolean true acceso para eliminar registros
     *  false denegado
     */
    public function get_perfdet_acceliminar() : bool {
        return $this->perfdet_acceliminar;
    }

    /**
     *
     * @return boolean true acceso para imprimir
     *  false denegado
     */
    public function get_perfdet_accimprimir() : bool {
        return $this->perfdet_accimprimir;
    }

    /**
     * Setea el acceso de lectura de registros
     *
     * @param boolean $perfdet_accleer true acceso para leer registros
     * de lo contrario denegado.
     */
    public function set_perfdet_accleer(bool $perfdet_accleer) : void {
        $this->perfdet_accleer = self::getAsBool($perfdet_accleer);
    }

    /**
     * Setea el acceso de agregar de registros
     *
     * @param boolean $perfdet_accagregar true acceso para leer agregar registros
     * de lo contrario denegado.
     */
    public function set_perfdet_accagregar(bool $perfdet_accagregar) : void {
        $this->perfdet_accagregar = self::getAsBool($perfdet_accagregar);
    }

    /**
     * Setea el acceso de actualizar registros
     *
     * @param boolean $perfdet_accactualizar true acceso para actualizar registros
     * de lo contrario denegado.
     */
    public function set_perfdet_accactualizar(bool $perfdet_accactualizar) : void {
        $this->perfdet_accactualizar = self::getAsBool($perfdet_accactualizar);
    }

    /**
     * Setea el acceso de eliminar registros
     *
     * @param boolean $perfdet_acceliminar true acceso para eliminar registros
     * de lo contrario denegado.
     */
    public function set_perfdet_acceliminar(bool $perfdet_acceliminar) : void {
        $this->perfdet_acceliminar = self::getAsBool($perfdet_acceliminar);
    }

    /**
     * Setea el acceso de impresion
     *
     * @param boolean $perfdet_accimprimir true acceso para impresion
     * de lo contrario denegado.
     */
    public function set_perfdet_accimprimir(bool $perfdet_accimprimir) : void {
        $this->perfdet_accimprimir = self::getAsBool($perfdet_accimprimir);
    }

    public function &getPKAsArray() : array {
        $pk['perfdet_id'] = $this->getId();
        return $pk;
    }

}
