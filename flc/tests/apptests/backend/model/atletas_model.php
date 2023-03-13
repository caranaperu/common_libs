<?php

namespace flc\tests\apptests\backend\model;

require_once '/var/www/common/flc/tests/apptests/backend/accessors/atletas_accessor.php';


use flc\core\dto\flcInputDataProcessor;
use flc\core\flcServiceLocator;
use flc\core\model\flcBaseModel;
use flc\database\driver\flcDriver;


class  atletas_model extends flcBaseModel {
    public function __construct(flcDriver $p_driver, ?flcInputDataProcessor $p_input_data) {
        $this->fields = [
            'atletas_codigo' => null,
            'atletas_ap_paterno' => null,
            'atletas_ap_materno' => null,
            'atletas_nombres' => null,
            'atletas_nombre_completo' => null,
            'atletas_sexo' => null,
            'atletas_nro_documento' => null,
            'atletas_nro_pasaporte' => null,
            'paises_codigo' => null,
            'atletas_fecha_nacimiento' => null,
            'atletas_telefono_casa' => null,
            'atletas_telefono_celular' => null,
            'atletas_email' => null,
            'atletas_direccion' => null,
            'atletas_observaciones' => null,
            'atletas_talla_ropa_buzo' => null,
            'atletas_talla_ropa_poloshort' => null,
            'atletas_talla_zapatillas' => null,
            'atletas_norma_zapatillas' => null,
            'atletas_url_foto' => null,
            'atletas_protected' => false,
            'activo' => true,
            'usuario' => null,
            'fecha_creacion' => null,
            'usuario_mod' => null,
            'fecha_modificacion' => null,
            'xmin' => null // this is a rowversion id for this model
        ];



        $this->fields_computed = ['atletas_agno' => null];
        $this->fields_operations = [
            'atletas_agno' => 'c',
            'atletas_nombre_completo' => 'rf',
            'usuario_mod' => 'u',
            'fecha_modificacion' => 'u',
            'atletas_protected' => 'rf'
        ];

        //$this->fields_ro = ['name' => null];

        $this->key_fields = ['atletas_codigo'];
        $this->table_name = 'tb_atletas';
        $this->field_types = [
            'atletas_talla_zapatillas' => 'nostring',
            'atletas_protected' => 'bool',
            'activo' => 'bool',
            'atletas_agno' => 'nostring',
            'xmin' => 'rowversion'
        ];

        //$this->accessor = new atletas_accessor($p_driver);
        $this->accessor = flcServiceLocator::get_instance()->service('accessor','flc\tests\apptests\backend\accessors\atletas_accessor',$p_driver);


        parent::__construct($p_driver, $p_input_data);

    }
}