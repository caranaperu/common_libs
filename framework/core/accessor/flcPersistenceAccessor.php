<?php

namespace framework\core\accessor;

use framework\core\model\flcBaseModel;

abstract class  flcPersistenceAccessor {
    static array $open_close_flags = [
        'DB_OPEN_FLAG' => 0x0001,
        'DB_CLOSE_FLAG' => 0x0002,

    ];

    public abstract function add(array $p_id, array $p_fields): bool;

    public abstract function update(flcBaseModel &$p_model, ?string $p_suboperation = null, int $p_open_close = 0): int;

    public abstract function delete(array $p_fields, array $p_constraints, string $p_type): bool;

    public abstract function read(flcBaseModel &$p_model, ?string $p_suboperation = null, int $p_open_close = 0): int;

    public abstract function fetch(array $p_fields, array $p_constraints, string $p_suboperation): bool;


}