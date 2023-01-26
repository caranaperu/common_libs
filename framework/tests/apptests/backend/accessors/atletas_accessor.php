<?php

namespace framework\tests\apptests\backend\accessors;


use framework\core\accessor\constraints\flcConstraints;
use framework\core\accessor\flcDbAccessor;
use framework\core\entity\flcBaseEntity;

class atletas_accessor extends flcDbAccessor {

    protected function get_fetch_query_full(flcBaseEntity $p_entity, ?array $p_ref_entities, ?flcConstraints $p_constraints = null, ?string $p_suboperation = null): string {

        // exclude protected records
        if ($p_constraints == null) {
            $p_constraints = new flcConstraints();
        }
        $p_constraints->add_where_field(['atletas_protected', '!=']);

        $p_entity->atletas_protected = true;


        // generate query
        $sql = $this->select_clause($p_entity->get_fields(), $p_entity->get_table_name(), $p_constraints);
        [$select, $from] = explode('from', $sql);
        $sql = $select.', EXTRACT(YEAR FROM atletas_fecha_nacimiento)::CHARACTER VARYING AS atletas_agno from '.$from;
        $sql = 'select * from ('.$sql.') a';

        // where and order by

        // all fields including computed in case is part of the where or order by clause.
        $fields_ext = $p_entity->get_all_fields('c');
        $sql .= $this->where_clause($fields_ext, $p_entity->get_field_types(), $p_constraints);
        $sql .= $this->order_by_clause($fields_ext, $p_entity->get_key_fields(), $p_entity->get_id_field(), $p_constraints);

        // limit offset (warning , is better with an order by
        $sql .= ' '.$this->db->get_limit_offset_str($p_constraints->get_start_row(), $p_constraints->get_end_row());


        //echo $sql;
        return $sql;

    }

    protected function get_add_query(flcBaseEntity $p_entity, ?string $p_suboperation = null): string {

        return  $this->db->callable_string_extended('sp_atletas_save_record', 'function', 'scalar', [
            'p_atletas_codigo' => $p_entity->atletas_codigo,
            'p_atletas_ap_paterno' => $p_entity->atletas_ap_paterno,
            'p_atletas_ap_materno' => $p_entity->atletas_ap_materno,
            'p_atletas_nombres' => $p_entity->atletas_nombres,
            'p_atletas_sexo' => $p_entity->atletas_sexo,
            'p_atletas_nro_documento' => $p_entity->atletas_nro_documento,
            'p_atletas_nro_pasaporte' => $p_entity->atletas_nro_pasaporte,
            'p_paises_codigo' => $p_entity->paises_codigo,
            'p_atletas_fecha_nacimiento' => $p_entity->atletas_fecha_nacimiento,
            'p_atletas_telefono_casa' => $p_entity->atletas_telefono_casa,
            'p_atletas_telefono_celular' => $p_entity->atletas_telefono_celular,
            'p_atletas_email' => $p_entity->atletas_email,
            'p_atletas_direccion' => $p_entity->atletas_direccion,
            'p_atletas_observaciones' => $p_entity->atletas_observaciones,
            'p_atletas_talla_ropa_buzo' => $p_entity->atletas_talla_ropa_buzo,
            'p_atletas_talla_ropa_poloshort' => $p_entity->atletas_talla_ropa_poloshort,
            'p_atletas_talla_zapatillas' => $p_entity->atletas_talla_zapatillas,
            'p_atletas_norma_zapatillas' => $p_entity->atletas_norma_zapatillas,
            'p_atletas_url_foto' => $p_entity->atletas_url_foto,
            'p_activo' => $p_entity->activo,
            'p_usuario' => $p_entity->usuario,
            'p_version_id' => null,
            'p_is_update' => false
        ]);

    }

    protected function get_read_query(flcBaseEntity $p_entity, ?string $p_suboperation = null, ?flcConstraints $p_constraints = null): string {
        $sql =  parent::get_read_query($p_entity, $p_suboperation, $p_constraints);
        [$select, $from] = explode('from', $sql);
        $sql = $select.', EXTRACT(YEAR FROM atletas_fecha_nacimiento)::CHARACTER VARYING AS atletas_agno from '.$from;


        return $sql;
    }
}