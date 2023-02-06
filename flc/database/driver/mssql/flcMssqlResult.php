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

namespace flc\database\driver\mssql;

use flc\database\driver\flcDriver;
use flc\database\flcDbResult;
use stdClass;


/**
 * MS SQL server  Result Class
 *
 * This class extends the parent result class: flc\database\flcDbResult
 * and is the specific one for ms sql server.
 *
 * Important : This class is a modified one of postgre_result from codeigniter
 * all credits for his authors.
 */
class flcMssqlResult extends flcDbResult {
    /**
     * Scrollable flag
     *
     * @var    mixed
     */
    public $scrollable;

    /**
     * List of field types based on numeric type returned by sqlsrv_field_metadata
     * @link https://docs.microsoft.com/en-us/sql/connect/php/sqlsrv-field-metadata?view=sql-server-ver16
     *
     * @var array|string[]
     */
    private static array $_fieldTypes = [
        -5 => 'bigint',
        -2 => 'binary',
        -7 => 'bit',
        1 => 'char',
        91 => 'date',
        93 => 'datetime',
        -155 => 'datetimeoffset4',
        3 => 'decimal',
        6 => 'float',
        -4 => 'image',
        4 => 'int',
        -8 => 'nchar',
        2 => 'numeric',
        -9 => 'nvarchar',
        -10 => 'ntext',
        7 => 'real',
        5 => 'smallint',
        -1 => 'text',
        -154 => 'time',
        -2 => 'timestamp',
        -6 => 'tinyint',
        -11 => 'uniqueidentifier',
        -151 => 'udt',
        -3 => 'varbinary',
        12 => 'varchar',
        -152 => 'xml'
    ];

    /**
     * Constructor
     *
     * @param flcDriver $p_db_driver the flcDriver instance handling the results
     * @param resource  $p_result_id the result_id resource returned after execute a query on database
     */
    public function __construct(flcDriver $p_db_driver, $p_result_id) {
        parent::__construct($p_db_driver,$p_result_id);
        $this->scrollable = $p_db_driver->scrollable;
    }

    /**
     * IMPORTANT:
     * If you are using a forward cursor for example sqlsrv_num_rows() returns
     * 0 , the method in this case read the data on the cache and count the records
     * via parent::num_rows().
     * But this is a problem if after call this method we call unbuffered_row() because
     * at that moment num_rows put the cursor on the last record , and of course unbuffered_record()
     * return always null.
     *
     * @inheritdoc
     */
    public function num_rows(): int {
        // Based on the sqlsrv documentation :
        // sqlsrv_num_rows requires a client-side, static, or keyset cursor, and will return false
        // if you use a forward cursor or a dynamic cursor. (A forward cursor is the default.)
        if (!in_array($this->scrollable, [
            FALSE,
            SQLSRV_CURSOR_FORWARD,
            SQLSRV_CURSOR_DYNAMIC
        ], TRUE)) {
            return parent::num_rows();
        }

        return ($this->num_rows > 0) ? $this->num_rows : $this->num_rows = sqlsrv_num_rows($this->result_id);
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function num_fields(): int {
        return @sqlsrv_num_fields($this->result_id);
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function list_fields(): array {
        $field_names = [];
        foreach (sqlsrv_field_metadata($this->result_id) as $offset => $field) {
            $field_names[] = $field['Name'];
        }

        return $field_names;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function field_data(): array {
        $retval = [];
        foreach (sqlsrv_field_metadata($this->result_id) as $i => $field) {
            $retval[$i] = new stdClass();
            $retval[$i]->name = $field['Name'];
            $retval[$i]->type = flcMssqlResult::$_fieldTypes[$field['Type']];

            $retval[$i]->max_length = $field['Size'];
        }

        return $retval;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function free_result(): void {
        if (is_resource($this->result_id)) {
            sqlsrv_free_stmt($this->result_id);

            $this->result_id = null;
        }
    }


    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _fetch_assoc() {
        return sqlsrv_fetch_array($this->result_id, SQLSRV_FETCH_ASSOC);
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _fetch_object(string $p_classname = 'stdClass'): ?object {
        $obj = sqlsrv_fetch_object($this->result_id, $p_classname);

        return is_object($obj) ? $obj : null;
    }

    // --------------------------------------------------------------------


    /**
     * @inheritdoc
     */
    public function affected_rows() : int {
        return sqlsrv_rows_affected($this->result_id);
    }



}
