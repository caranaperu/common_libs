<?php

namespace framework\tests\apptests\backend\model;

use framework\core\accessor\flcDbAccessor;
use framework\core\dto\flcInputData;
use framework\core\entity\flcBaseEntity;
use framework\database\driver\flcDriver;

class  paises_model extends flcBaseEntity {
    public function __construct(flcDriver $p_driver, ?flcInputData $p_input_data) {
        $this->fields = [
            'paises_codigo' => null,
            'paises_descripcion' => null,
            'paises_entidad' => false,
            'paises_use_apm' => true,
            'paises_use_docid' => true,
            'regiones_codigo' => null,
            'activo' => true,
            'usuario' => null,
            'fecha_creacion' => null,
            'usuario_mod'  => null,
            'fecha_modificacion' => null
        ];

        //$this->fields_ro = ['name' => null];

        $this->key_fields = ['paises_codigo'];
        $this->table_name = 'tb_paises';
        $this->field_types = ['paises_entidad' => 'bool', 'paises_use_apm' => 'bool', 'activo' => 'bool'];

        $this->accessor = new flcDbAccessor($p_driver);

        parent::__construct($p_driver, $p_input_data);

    }
}