<?php
/**
 * This file is part of Future Labs Code 1 framework.
 **
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @author Carlos Arana Reategui.
 */

namespace framework\core\accessor;

use framework\core\accessor\constraints\flcConstraints;
use framework\core\entity\flcBaseEntity;

/**
 * Class that defines the operations that can be do it from the entities or models to the persistence.
 *
 */
abstract class  flcPersistenceAccessor {


    /**
     * Add an instance of the entity to the persistence.
     *
     * @param flcBaseEntity       $p_entity the entity to add to the persistence
     * @param string|null         $p_suboperation optional user defined suboperation.
     * @param flcConstraints|null $p_constraints constraints only to be used on the read record after add
     * if the implementation support that stuff , if no constraints exist the reread record
     * if implemented can read joined files.
     *
     * @return int with the error code.
     */
    public abstract function add(flcBaseEntity &$p_entity, ?string $p_suboperation = null, ?flcConstraints $p_constraints = null): int;

    /**
     * update an instance of the entity on the persistence.
     *
     * @param flcBaseEntity $p_entity the entity to update to the persistence
     * @param string|null   $p_suboperation optional user defined suboperation.
     * @param flcConstraints|null $p_constraints constraints only to be used on the read record after add
     * if the implementation support that stuff , if no constraints exist the reread record
     * if implemented can read joined files.
     *
     * @return int with the error code.
     */
    public abstract function update(flcBaseEntity &$p_entity, ?string $p_suboperation = null, ?flcConstraints $p_constraints = null): int;

    /**
     * delete an instance of the entity from the persistence.
     *
     * @param flcBaseEntity $p_entity the entity to delete from the persistence
     * @param bool          $p_verify_deleted_check an error will be returned if already deleted the entity.
     *
     * @return int with the error code.
     */
    public abstract function delete(flcBaseEntity &$p_entity, bool $p_verify_deleted_check = true): int;

    /**
     * delete an instance or multiple instances from the persistence, based on the constraints
     *
     * @param flcBaseEntity  $p_entity the entity to delete from the persistence
     * @param flcConstraints $p_constraints
     *
     * @return int with the error code.
     */
    public abstract function delete_full(flcBaseEntity &$p_entity, flcConstraints $p_constraints): int;

    /**
     * read an instance of the entity from the persistence.
     *
     * @param flcBaseEntity       $p_entity the entity to read from the persistence
     * @param string|null         $p_suboperation optional user defined suboperation.
     * @param flcConstraints|null $p_constraints the constraints to use reading , useful when joins exist.
     *
     * @return int with the error code.
     */
    public abstract function read(flcBaseEntity &$p_entity, ?string $p_suboperation = null, ?flcConstraints $p_constraints = null): int;

    /**
     * Read a set of instances from the persistence based on the constraints,joins are allowed but the were
     * conditions only work for the main entity (table).
     *
     * @param flcBaseEntity       $p_entity with the values requiered for the constraints
     * @param flcConstraints|null $p_constraints the constraints to use on the fecth clauses.
     * @param string|null         $p_suboperation optional user defined suboperation.
     *
     * @return array|array[]|int an array of results or an error code.
     */
    public abstract function fetch(flcBaseEntity $p_entity, ?flcConstraints $p_constraints = null, ?string $p_suboperation = null);

    /**
     * Read a set of instances from the persistence based on the constraints, but this one allow joins of fields
     * from another entities.also  were conditions with other entities (tables) referenced fields.
     *
     * @param flcBaseEntity       $p_entity with the values requiered for the constraints
     * @param array|null          $p_ref_entities an array of entities in case we need to reference other table fields
     *     in constraints.
     * @param flcConstraints|null $p_constraints the constraints to use on the fecth clauses.
     * @param string|null         $p_suboperation optional user defined suboperation.
     *
     * @return array|array[]|int an array of results or an error code.
     *
     */
    public abstract function fetch_full(flcBaseEntity $p_entity, ?array $p_ref_entities, ?flcConstraints $p_constraints = null, ?string $p_suboperation = null);


}