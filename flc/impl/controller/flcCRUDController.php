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
namespace flc\impl\controller;


use flc\core\accessor\flcPersistenceAccessorAnswer;
use flc\core\FLC;
use RuntimeException;


/**
 * Standard class to CRUD operations over model.
 */
abstract class flcCRUDController extends flcBaseController {


    /**
     * @inheritdoc
     *
     * Do the add,delete,update and fetch operations over the model and the persistence.
     */
    protected function execute_operation(string $p_operation, string $p_sub_operation): flcPersistenceAccessorAnswer {

        if ($p_operation == 'fetch') {
            // prepare otuput data
            $this->output_data->set_start_row($this->input_data_processor->get_start_row());
            $this->output_data->set_end_row($this->input_data_processor->get_end_row());

            $answer = $this->model->fetch($p_sub_operation);

        } elseif ($p_operation == 'add') {
            $answer = $this->model->add($p_sub_operation);
        } elseif ($p_operation == 'upd') {
            $answer = $this->model->update($p_sub_operation);

        } elseif ($p_operation == 'del') {
            $answer = $this->model->delete($p_sub_operation);
        } else {
            throw new RuntimeException("Operation $p_operation is not supported");
        }

        // Clean persistence
        FLC::get_instance()->db->trans_complete();
        FLC::get_instance()->db->close();

        return $answer;
    }

}