<?php
/**
 * This file is part of Future Labs Code 1 framework.
 **
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @author Carlos Arana Reategui.
 */

namespace flc\core\accessor;

use flc\core\accessor\constraints\flcConstraints;
use flc\core\model\flcBaseModel;

/**
 * Class that defines the operations that can be do it from the entities or models to the persistence.
 *
 */
abstract class  flcPersistenceAccessor {


    /**
     * Add an instance of the model to the persistence.
     *
     * @param flcBaseModel        $p_model the model to add to the persistence
     * @param string|null         $p_suboperation optional user defined suboperation.
     * @param flcConstraints|null $p_constraints constraints only to be used on the read record after add
     * if the implementation support that stuff , if no constraints exist the reread record
     * if implemented can read joined files.
     *
     * @return flcPersistenceAccessorAnswer with the answer.
     * @see flcPersistenceAccessorAnswer
     */
    public abstract function add(flcBaseModel &$p_model, ?string $p_suboperation = null, ?flcConstraints $p_constraints = null): flcPersistenceAccessorAnswer;

    /**
     * update an instance of the model on the persistence based on his keys or id.
     *
     * @param flcBaseModel        $p_model the model to update to the persistence
     * @param string|null         $p_suboperation optional user defined suboperation.
     * @param flcConstraints|null $p_constraints constraints only to be used on the read record after add
     * if the implementation support that stuff , if no constraints exist the reread record
     * if implemented can read joined files.
     *
     * @return flcPersistenceAccessorAnswer with the answer.
     * @see flcPersistenceAccessorAnswer
     */
    public abstract function update(flcBaseModel &$p_model, ?string $p_suboperation = null, ?flcConstraints $p_constraints = null): flcPersistenceAccessorAnswer;

    /**
     * delete an instance of the model from the persistence.
     *
     * @param flcBaseModel $p_model the model to delete from the persistence
     * @param bool         $p_verify_deleted_check an error will be returned if already deleted the model.
     *
     * @return flcPersistenceAccessorAnswer with the answer.
     * @see flcPersistenceAccessorAnswer
     */
    public abstract function delete(flcBaseModel &$p_model, bool $p_verify_deleted_check = true): flcPersistenceAccessorAnswer;

    /**
     * delete an instance or multiple instances from the persistence, based on the constraints
     *
     * @param flcBaseModel   $p_model the model to delete from the persistence
     * @param flcConstraints $p_constraints
     *
     * @return flcPersistenceAccessorAnswer with the answer.
     * @see flcPersistenceAccessorAnswer
     */
    public abstract function delete_full(flcBaseModel &$p_model, flcConstraints $p_constraints): flcPersistenceAccessorAnswer;

    /**
     * read an instance of the model from the persistence.
     *
     * @param flcBaseModel        $p_model the model to read from the persistence
     * @param string|null         $p_suboperation optional user defined suboperation.
     * @param flcConstraints|null $p_constraints the constraints to use reading , useful when joins exist.
     *
     * @return flcPersistenceAccessorAnswer with the answer.
     * @see flcPersistenceAccessorAnswer
     */
    public abstract function read(flcBaseModel &$p_model, ?string $p_suboperation = null, ?flcConstraints $p_constraints = null): flcPersistenceAccessorAnswer;

    /**
     * Read a set of instances from the persistence based on the constraints,joins are allowed but the were
     * conditions only work for the main model (table).
     *
     * @param flcBaseModel        $p_model with the values requiered for the constraints
     * @param flcConstraints|null $p_constraints the constraints to use on the fecth clauses.
     * @param string|null         $p_suboperation optional user defined suboperation.
     *
     * @return flcPersistenceAccessorAnswer with the answer.
     * @see flcPersistenceAccessorAnswer
     */
    public abstract function fetch(flcBaseModel $p_model, ?flcConstraints $p_constraints = null, ?string $p_suboperation = null) : flcPersistenceAccessorAnswer;

    /**
     * Read a set of instances from the persistence based on the constraints, but this one allow joins of fields
     * from another entities.also  were conditions with other entities (tables) referenced fields.
     *
     * @param flcBaseModel        $p_model with the values requiered for the constraints
     * @param array|null          $p_ref_models an array of entities in case we need to reference other table fields
     *     in constraints.
     * @param flcConstraints|null $p_constraints the constraints to use on the fecth clauses.
     * @param string|null         $p_suboperation optional user defined suboperation.
     *
     * @return flcPersistenceAccessorAnswer with the answer.
     * @see flcPersistenceAccessorAnswer
     *
     */
    public abstract function fetch_full(flcBaseModel $p_model, ?array $p_ref_models, ?flcConstraints $p_constraints = null, ?string $p_suboperation = null) : flcPersistenceAccessorAnswer;


}