<?php

namespace app\common\dao\impl;

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Este DAO es especifico para el manejo de los header de perfiles al sistema,
 * todo sistema que implementa perfiles hara uso de este DAO , este caso
 * especifico es la implementacion para POSTGRES SQL y debera ser adaptada
 * para otras bases de datos , lo unico especifico aqui es el uso del
 * campo xmin.
 *
 * La tabla debera ser la siguiente o su equivalente en otras bases.
 *
 *       -- Table: tb_sys_perfil
 *
 *      -- DROP TABLE tb_sys_perfil;
 *
 * CREATE TABLE tb_sys_perfil_detalle
 * (
 *   perfdet_id integer NOT NULL DEFAULT nextval('tb_sys_perfdet_id_seq'::regclass),
 *   perfdet_accessdef character varying(10),
 *   perfdet_accleer boolean NOT NULL DEFAULT false,
 *   perfdet_accagregar boolean NOT NULL DEFAULT false,
 *   perfdet_accactualizar boolean NOT NULL DEFAULT false,
 *   perfdet_acceliminar boolean NOT NULL DEFAULT false,
 *   perfdet_accimprimir boolean NOT NULL DEFAULT false,
 *   perfil_id integer,
 *   menu_id integer NOT NULL,
 *   activo boolean NOT NULL DEFAULT true,
 *   usuario character varying(15) NOT NULL,
 *   fecha_creacion timestamp without time zone NOT NULL,
 *   usuario_mod character varying(15),
 *   fecha_modificacion timestamp without time zone,
 *  CONSTRAINT pk_perfdet_id PRIMARY KEY (perfdet_id ),
 *   CONSTRAINT fk_perfdet_perfil FOREIGN KEY (perfil_id)
 *      REFERENCES tb_sys_perfil (perfil_id) MATCH SIMPLE
 *       ON UPDATE NO ACTION ON DELETE NO ACTION
 *)
 * WITH (
 *   OIDS=FALSE
 * );
 * ALTER TABLE tb_sys_perfil_detalle
 *   OWNER TO muniren;
 *
 * @version $Id: TSLAppPerfilDetalleDAO_postgre.php 4 2014-02-11 03:31:42Z aranape $
 * @history  11-02-2017  manejo del booleano se ajusto en el add y update.
 *
 */
class TSLAppPerfilDetalleDAO_postgre extends \app\common\dao\TSLAppBasicRecordDAO_postgre {

    /**
     * Constructor se puede indicar si las busquedas solo seran en registros activos.
     * @param boolean $activeSearchOnly
     */
    public function __construct($activeSearchOnly = TRUE) {
        parent::__construct($activeSearchOnly);
    }

    /**
     * @inheritdoc
     */
    protected function getDeleteRecordQuery($id, int $versionId) : string {
        return 'delete from tb_sys_perfil_detalle  where perfdet_id = \'' . $id . '\'  and xmin =' . $versionId;
    }

    /**
     * @inheritdoc
     */
    protected function getAddRecordQuery(\TSLDataModel &$record, \TSLRequestConstraints &$constraints = NULL) : string {
        /* @var $record \app\common\model\impl\TSLAppPerfilDetalleModel*/
        $sql = 'insert into tb_sys_perfil_detalle (perfil_id,perfil_id,perfdet_accessdef,activo,usuario) values(' .
                $record->getId() . ',' .
                $record->get_perfil_id() . '\',\'' .
                $record->get_perfdet_accessdef() . '\',\'' .
                $record->getActivo() . '\',\'' .
                $record->getUsuario() . '\')';

        return $sql;
    }

    /**
     * HAce un fetch a todos los registros de 2 manera la normal y la fetchWithAccess , esta segunda
     * hace un join al menu para determinar los accesos permitidos , es basicamente para uso del GUI
     * al definir los perfiles.
     *
     * @inheritdoc
     */
    protected function getFetchQuery(\TSLDataModel &$record = NULL, \TSLRequestConstraints &$constraints = NULL, string $subOperation = NULL) : string {
        $sql = 'select perfdet_id,perfil_id,perfdet_accessdef,perfdet_accleer,perfdet_accagregar,perfdet_accactualizar,perfdet_acceliminar,' .
                'perfdet_accimprimir,pd.activo,pd.xmin as "versionId" ';
        if ($subOperation === 'fetchWithAccess') {
            $sql .= ',pd.menu_id,menu_accesstype ,menu_descripcion,menu_parent_id,menu_orden from tb_sys_perfil_detalle pd  ';
            $sql .= 'left join tb_sys_menu m on m.menu_id = pd.menu_id  ';
        } else {
            $sql .= ' from tb_sys_perfil_detalle pd  ';
        }



        if ($this->activeSearchOnly == TRUE) {
            // Solo activos
            $sql .= ' where pd.activo=TRUE ';
        }

        $where = $constraints->getFilterFieldsAsString();
        if (strlen($where) > 0) {
            $sql .= ' and ' . $where;
        }

        // Si la solicitud es la lista preparada con join para basicamente el GUI
        // el orden siempre sera menu_orden
        if ($subOperation === 'fetchWithAccess') {
            $sql .= ' order by menu_orden';
        } else {

            if (isset($constraints)) {
                $orderby = $constraints->getSortFieldsAsString();
                if ($orderby !== NULL) {
                    $sql .= ' order by ' . $orderby;
                }
            }
        }

        // Chequeamos paginacion
        $startRow = $constraints->getStartRow();
        $endRow = $constraints->getEndRow();

        if ($endRow > $startRow) {
            $sql .= ' LIMIT ' . ($endRow - $startRow) . ' OFFSET ' . $startRow;
        }

        return $sql;
    }

    /**
     * @inheritdoc
     */
    protected function getRecordQuery($id,\TSLRequestConstraints &$constraints = NULL, string $subOperation = NULL) : string {
        return 'select perfdet_id,perfil_id,perfdet_accessdef,perfdet_accleer,perfdet_accagregar,perfdet_accactualizar,perfdet_acceliminar,' .
                'perfdet_accimprimir,activo,xmin as "versionId" from tb_sys_perfil_detalle where perfdet_id =' . $id;
    }

    /**
     * @inheritdoc
     */
    protected function getRecordQueryByCode($code,\TSLRequestConstraints &$constraints = NULL, string $subOperation = NULL) : string {
        return $this->getRecordQuery($code);
    }

    /**
     * La metodologia para el update es un hack por problemas en el psotgresql cuando un updaate
     * es llevado a una function procedure , recomendamos leer el stored procedure.
     *
     * @inheritdoc
     */
    protected function getUpdateRecordQuery(\TSLDataModel &$record)  : string {

        /* @var $record \app\common\model\impl\TSLAppPerfilDetalleModel */
        $sql = 'select * from (select sp_perfil_detalle_save_record(' .
                $record->get_perfdet_id() . ',' .
                $record->get_perfil_id() . ',' .
                $record->get_menu_id() . ',' .
                '\'' . ($record->get_perfdet_accleer() != TRUE ? '0' : '1') . '\'::boolean,' .
                '\'' . ($record->get_perfdet_accagregar() != TRUE ? '0' : '1')  . '\'::boolean,' .
                '\'' . ($record->get_perfdet_accactualizar() != TRUE ? '0' : '1')  . '\'::boolean,' .
                '\'' . ($record->get_perfdet_acceliminar() != TRUE ? '0' : '1')  . '\'::boolean,' .
                '\'' . ($record->get_perfdet_accimprimir() != TRUE ? '0' : '1')  . '\'::boolean,' .
                '\'' . $record->getActivo() . '\'::boolean,' .
                '\'' . $record->get_Usuario_mod() . '\'::varchar,' .
                $record->getVersionId() . ') as insupd) as ans where insupd is not null;';
        return $sql;
    }

}

?>