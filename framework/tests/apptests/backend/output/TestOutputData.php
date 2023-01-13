<?php

namespace framework\tests\apptests\backend\output;

use framework\core\dto\flcOutputData;
use framework\core\entity\flcBaseEntity;

/**
 * Clase que implemente el JSON encoding para el DTO, ESTE ES EL DEFAULT
 * sino se implementa ninguno en especial
 *
 * @author Carlos Arana Reategui
 * @version 1.00 , 15 JUN 2011
 *          1.01 , 15 Mayo 2013 , se hace que siempre los errores se devuelvan como
 *                  arreglo para facilitar el trabajo del cliente
 *
 * @since 1.00
 *
 * Por ejemplo si es un query a la persistencia y no hay errores se retornara lo siguiente
 * <pre>
 * {
 *      "response":
 *                  {"status":0,
 *                      "data":[
 *                              {"field1":"field1_value","field2":"field2_value"},
 *                              {"field1":"field1_value","field2":"field2_value"}
 *                             ],
 *                          "endRow" :  "2",
 *                          "totalRows": "2"}
 *                  }
 * }
 * </pre>
 *
 */
class TestOutputData {

    /**
     * Generate json output for smartclient
     *
     * @param flcOutputData $p_output_data wiht the data to process
     *
     * @return mixed en este caso un String con el DTO en formato JSON
     */
    public function &process(flcOutputData $p_output_data) {
        $out = null;

        if (strlen($p_output_data->get_answer_message()) > 0) {
            // STATUS_OK = 0
            $out = '"status":-1';
            $out .= ',"data":"'.$p_output_data->get_answer_message().'- Cod.Error: '.$p_output_data->get_error_code().'"';
        }

        if (!$p_output_data->is_success()) {

            if ($p_output_data->has_field_errors()) {
                // VALIDATION ERRORS
                $out = 'status:"-4"';
                $fldErrors = $p_output_data->get_field_errors();

                // Si ya tiene longitud , ponemos una coma para indicar
                // un nuevo elemento.
                if (strlen($out) > 0) {
                    $out .= ',';
                }

                // la lista de field errors.
                $out .= '"errors":{';

                foreach ($fldErrors as $field => $msg) {
                    $out .= $field.':"'.$msg.'",';
                }
                // remove trailing comma
                $out = substr($out, 0, strrpos($out, ','));
                $out .= '}';
            } else {
                if ($p_output_data->has_process_errors()) {
                    // STATUS_FAILURE = -1
                    $out = '"status":-1,';

                    $process_errors = $p_output_data->get_process_errors();

                    // la lista de process errors.
                    $out .= '"data":';
                    $count = count($process_errors);

                    for ($i = 0; $i < $count; $i++) {
                        if ($i > 0) {
                            $out .= '\n';
                        }
                        $out .= '"';

                        $perr = str_replace(["\"", "\r", "\n", "\r\n"], ' ', $process_errors[$i][1]);
                        // Si tiene excepcion procesamos.
                        $ex = $process_errors[$i][2];
                        if (isset($ex)) {
                            if (strlen(trim($perr)) > 0) {
                                $out .= $perr.' - '.str_replace([
                                        "\"",
                                        "\r",
                                        "\n",
                                        "\r\n"
                                    ], ' ', $ex->getMessage()).' ** CodError = '.$process_errors[$i][1];
                            } else {
                                $out .= str_replace([
                                        "\"",
                                        "\r",
                                        "\n",
                                        "\r\n"
                                    ], ' ', $ex->getMessage()).' ** CodError ='.$process_errors[$i][1];
                            }
                        } else {
                            $out .= $perr.' ** CodError ='.$process_errors[$i][1];
                        }

                        $out .= '"';
                        if ($i < $count - 1) {
                            $out .= ',';
                        }
                    }
                    $out .= '';
                }
            }
        } else {
            // STATUS_OK = 0
            if ($out == null) {
                $out = '"status":0';
            }
        }
        // Si tiene parametros de salida los agregamos  antres de la data.
        $out_params = $p_output_data->get_output_parameters();
        if (is_array($out_params)) {
            foreach ($out_params as $i => $value) {
                $out .= ','.$i.':'.$value;
            }
        }

        // Si no hay errores de proceso evaluamos la data
        if (!$p_output_data->has_process_errors() && strlen($p_output_data->get_answer_message()) == 0) {
            $one_record = false;
            // Procesamos la data
            $data = $p_output_data->get_result_data();


            if (isset($data)) {
                // Si no es un arreglo solo posee un registro
                if (!is_array($data)) {
                    $one_record = true;
                }

                $out .= ',"data":';


                $this->_process_extra_data($data);
                $out .= json_encode($data);

                switch (json_last_error()) {
                    case JSON_ERROR_NONE:
                        break;
                    case JSON_ERROR_DEPTH:
                        break;
                    case JSON_ERROR_STATE_MISMATCH:
                        break;
                    case JSON_ERROR_CTRL_CHAR:
                        break;
                    case JSON_ERROR_SYNTAX:
                        break;
                    case JSON_ERROR_UTF8:
                        break;
                    default:
                        break;
                }

                // Numero de registros = al numero de registros leidos + la posicion inicial en el set
                // siempre que haya mas de una respuesta
                $numRecords = $one_record === false ? $p_output_data->get_start_row() + count($data) : 1;

                // SE hace de tal forma que si no es el ultimo registro osea numRecords es menor a la ultima fila solicitada
                // Ponemos como el total de registros una pagina mas (esto para evitar hacer un count)
                $out .= ',"endRow" : "'.$numRecords.'"';
                $out .= ',"totalRows": "'.(($numRecords < $p_output_data->get_end_row() || $p_output_data->get_end_row() == 0) ? ($numRecords) : $p_output_data->get_end_row() + ($p_output_data->get_end_row() - $p_output_data->get_start_row())).'"';
            } else {
                if ($out == null) {
                    // STATUS_OK = 0
                    $out = '"status":-1';
                    $out .= ',"data":"Error Desconocido"';
                }
            }
        }

        $out = '{"response":{'.$out.'}}';

        return $out;

    }


    /**
     * @param mixed | flcBaseEntity $p_extdata
     */
    private function _process_extra_data(&$p_extdata): void {
        if (isset($p_extdata)) {
            $extdata = $p_extdata;
            // IF not an array
            if (!is_array($p_extdata)) {
                // if is an object
                if (is_object($p_extdata)) {
                    if ($p_extdata instanceof flcBaseEntity) {
                        $p_extdata = $extdata->get_all_fields();
                    } else {
                        $p_extdata = (array)$extdata;
                    }
                }
            }
        }
    }

}
