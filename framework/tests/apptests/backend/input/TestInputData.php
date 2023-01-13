<?php

namespace framework\tests\apptests\backend\input;

use framework\core\dto\flcInputData;
use framework\core\entity\flcBaseEntity;

class TestInputData extends flcInputData {

    // add to the filter fields
    // EXACT
    // EXACTCASE
    // SUBSTRING (LIKE)
    // startsWith (LIKE)
    private static array $_operator_map = ['exact'=> '=','substring'=> 'ilike' ,'startsWith'=> 'ilike(-%)'];

    public function process_input_data(flcBaseEntity $p_entity) {
        // for easy and fast access
        $input_data =$this->input_data;

        $this->operation = $input_data['op'];
        if(isset($input_data['_operationId'])) {
            $this->sub_operation = $input_data['_operationId'];
        }

        // set the fields
        $fields = $p_entity->get_all_fields();
        foreach ($fields as $field => $value) {
            if (isset($input_data[$field])) {
                $this->fields[$field] = $input_data[$field];
            }
        }

        if ($this->operation == 'fetch') {
            $this->_process_constraints();
        }
    }

    private final function _process_constraints() {
        // for easy and fast access
        $input_data =$this->input_data;

        // pagination stuff
        $startRow = 0;
        $endRow = 0;

        if (isset($input_data['_startRow'])) {
            $startRow = $input_data['_startRow'];
        }

        if (isset($input_data['_endRow'])) {
            $endRow = $input_data['_endRow'];
        }

        $this->start_row = $startRow;
        $this->end_row = $endRow;

        // sort stuff
        if (isset($input_data['_sortBy'])) {
            $pos = strpos($input_data['_sortBy'], '-');
            if ($pos !== false) {
                $this->sort_fields[] = [trim($input_data['_sortBy'],'-'), 'desc'];
            } else {
                $this->sort_fields[] = [$input_data['_sortBy'], 'asc'];
            }
        }

        // constraint criteria
        if (isset($input_data['_acriteria'])) {
            $afilter = json_decode($input_data['_acriteria']);
            foreach ($afilter->criteria as $elem) {
                // set to the field list only if its already not defined.
                if (!isset($this->fields[$elem->fieldName]) || empty($this->fields[$elem->fieldName])) {
                    $this->fields[$elem->fieldName] = $elem->value;
                }
                // add to the filter fields
                $this->filter_fields[$elem->fieldName] = $elem->operator;
            }
        } else {
            // Los campos de filtro , para el smartClient son todos aquellos que
            // no tienen el underscore delante.
            foreach ($input_data as $key => $value) {
                // Si empieza con op o libid o parentId son parametros para otors usos no
                // son campos.
                if (!strcmp($key, "op") || !strcmp($key, "libid") || !strcmp($key, "parentId") || !strcmp($key, "filterSearchExact")) {
                    continue;
                }

                // Si no empieza con underscore o _isc (isomorphic smartclient identificador)
                $pos = strpos($key, '_');
                $pos2 = strpos($key, 'isc_');
                if (!($pos === 0 || $pos2 === 0)) {
                    if (!isset($this->fields[$key]) || empty($this->fields[$key])) {
                        $this->fields[$key] = $value;
                    }
                    if (isset($input_data['_textMatchStyle'])) {
                        // add to the filter fields
                        // EXACT
                        // EXACTCASE
                        // SUBSTRING (LIKE)
                        // startsWith (LIKE)
                        $op = self::$_operator_map[$input_data['_textMatchStyle']] ?? '=';

                        $this->filter_fields[$key] = $op;
                    } else {
                        $this->filter_fields[$key] = $value; // $value??????
                    }
                }
            }
        }

    }
}