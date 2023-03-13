<?php

namespace flc\tests\apptests\backend\accessors;


use flc\core\accessor\constraints\flcConstraints;
use flc\core\accessor\flcDbAccessor;
use flc\core\model\flcBaseModel;

class atletas_accessor extends flcDbAccessor {

    protected function get_fetch_query(flcBaseModel $p_model, ?flcConstraints $p_constraints = null, ?string $p_suboperation = null): string {

        // exclude protected records
        if ($p_constraints == null) {
            $p_constraints = new flcConstraints();
        }
        $p_constraints->add_where_field(['atletas_protected', '!=']);

        $p_model->atletas_protected = true;


        // generate query
        $sql = $this->select_clause($p_model->get_fields(), $p_model->get_table_name(), $p_constraints);
        [$select, $from] = explode('from', $sql);
        $sql = $select.', EXTRACT(YEAR FROM atletas_fecha_nacimiento)::CHARACTER VARYING AS atletas_agno from '.$from;
        $sql = 'select * from ('.$sql.') tb_atletas';

        // where and order by

        // all fields including computed in case is part of the where or order by clause.
        $fields_ext = $p_model->get_all_fields('c');
        $sql .= $this->where_clause($p_model->get_table_name(),$fields_ext, $p_model->get_field_types(), $p_constraints);
        $sql .= $this->order_by_clause($p_model->get_table_name(),$fields_ext, $p_model->get_key_fields(), $p_model->get_id_field(), $p_constraints);

        // limit offset (warning , is better with an order by
        $sql .= ' '.$this->db->get_limit_offset_str($p_constraints->get_start_row(), $p_constraints->get_end_row());


        //echo $sql;
        return $sql;

    }

    protected function get_add_query(flcBaseModel $p_model, ?string $p_suboperation = null): string {

        return  $this->db->callable_string_extended('sp_atletas_save_record', 'function', 'scalar', [
            'p_atletas_codigo' => $p_model->atletas_codigo,
            'p_atletas_ap_paterno' => $p_model->atletas_ap_paterno,
            'p_atletas_ap_materno' => $p_model->atletas_ap_materno,
            'p_atletas_nombres' => $p_model->atletas_nombres,
            'p_atletas_sexo' => $p_model->atletas_sexo,
            'p_atletas_nro_documento' => $p_model->atletas_nro_documento,
            'p_atletas_nro_pasaporte' => $p_model->atletas_nro_pasaporte,
            'p_paises_codigo' => $p_model->paises_codigo,
            'p_atletas_fecha_nacimiento' => $p_model->atletas_fecha_nacimiento,
            'p_atletas_telefono_casa' => $p_model->atletas_telefono_casa,
            'p_atletas_telefono_celular' => $p_model->atletas_telefono_celular,
            'p_atletas_email' => $p_model->atletas_email,
            'p_atletas_direccion' => $p_model->atletas_direccion,
            'p_atletas_observaciones' => $p_model->atletas_observaciones,
            'p_atletas_talla_ropa_buzo' => $p_model->atletas_talla_ropa_buzo,
            'p_atletas_talla_ropa_poloshort' => $p_model->atletas_talla_ropa_poloshort,
            'p_atletas_talla_zapatillas' => $p_model->atletas_talla_zapatillas,
            'p_atletas_norma_zapatillas' => $p_model->atletas_norma_zapatillas,
            'p_atletas_url_foto' => $p_model->atletas_url_foto,
            'p_activo' => $p_model->activo,
            'p_usuario' => $p_model->usuario,
            'p_version_id' => null,
            'p_is_update' => false
        ]);

    }

    protected function get_read_query(flcBaseModel $p_model, ?string $p_suboperation = null, ?flcConstraints $p_constraints = null): string {
        $sql =  parent::get_read_query($p_model, $p_suboperation, $p_constraints);
        [$select, $from] = explode('from', $sql);
        $sql = $select.', EXTRACT(YEAR FROM atletas_fecha_nacimiento)::CHARACTER VARYING AS atletas_agno from '.$from;


        return $sql;
    }
}