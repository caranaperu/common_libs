<?php
/**
 * This file is part of Future Labs Code 1 framework.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Inspired in codeigniter , all kudos for his authors
 *
 * @author Carlos Arana Reategui.
 *
 */

namespace flc\app\output;

use flc\core\dto\flcOutputData;
use flc\core\dto\flcOutputDataProcessor;
use flc\core\model\flcBaseModel;

/**
 * Class that implements data output processror specific for the smartclient
 * library.
 *
 *
 * If it is a query to the persistence and no errors the answer will be something like this:
 *
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
 * with errors will be something like this:
 *
 * <pre>
 * {
 *      "response":{status:"-4",
 *                      "errors":{
 *                              field1:" The field field1 need to be a decimal."}
 *                       }
 * }
 * </pre>
 *
 */
class SmartclientJsonOutputDataProcessor extends flcOutputDataProcessor {

    /**
     *
     * @inheritdoc
     */
    public function process_output_data(flcOutputData $p_output_data) {
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

                if (strlen($out) > 0) {
                    $out .= ',';
                }

                // the field errors list
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

                    // process errors.
                    $out .= '"data":';
                    $count = count($process_errors);

                    for ($i = 0; $i < $count; $i++) {
                        if ($i > 0) {
                            $out .= '\n';
                        }
                        $out .= '"';

                        $perr = str_replace(["\"", "\r", "\n", "\r\n"], ' ', $process_errors[$i][1]);
                        // if exception process
                        $ex = $process_errors[$i][2];
                        if (!empty($ex)) {
                            if (strlen(trim($perr)) > 0) {
                                $out .= $perr.' - '.str_replace([
                                        "\"",
                                        "\r",
                                        "\n",
                                        "\r\n"
                                    ], ' ', $ex->getMessage()).' ** CodError = '.$process_errors[$i][0];
                            } else {
                                $out .= str_replace([
                                        "\"",
                                        "\r",
                                        "\n",
                                        "\r\n"
                                    ], ' ', $ex->getMessage()).' ** CodError ='.$process_errors[$i][0];
                            }
                        } else {
                            $out .= $perr.' ** CodError ='.$process_errors[$i][0];
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

        // if have output parameters to output , we put befor data-
        $out_params = $p_output_data->get_output_parameters();
        if (is_array($out_params)) {
            foreach ($out_params as $i => $value) {
                $out .= ','.$i.':'.$value;
            }
        }

        // if doesnt exist any kind of error , process the data to uoutput
        if (!$p_output_data->has_process_errors() && strlen($p_output_data->get_answer_message()) == 0) {
            $one_record = false;
            // data procesing
            $data = $p_output_data->get_result_data();


            if (isset($data)) {
                // f not an array , only one record to output
                if (!is_array($data)) {
                    $one_record = true;
                }

                $out .= ',"data":';


                $this->_process_data($data);
                $out .= json_encode($data);


                //  number of recorsd = number of readed records + initial position , when exist more than one
                // record.
                $numRecords = $one_record === false ? $p_output_data->get_start_row() + count($data) : 1;

                // If not the last record => numRecords < the last record required , then the total number of records
                // we put as total recods one page more (to avoid to do count of records)
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

        return '{"response":{'.$out.'}}';

    }


    /**
     * Process the data to output as real answers , like a record or records obtaines from the
     *  persistence after a call from the client side.
     *
     * @param mixed | flcBaseModel $p_extdata
     */
    private function _process_data(&$p_extdata): void {
        if (isset($p_extdata)) {
            $extdata = $p_extdata;
            // IF not an array
            if (!is_array($p_extdata)) {
                // if is an object
                if (is_object($p_extdata)) {
                    if ($p_extdata instanceof flcBaseModel) {
                        $p_extdata = $extdata->get_all_fields();
                    } else {
                        $p_extdata = (array)$extdata;
                    }
                }
            }
        }
    }

}
