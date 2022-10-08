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

namespace framework\database\driver\mssql;

use framework\database\driver\flcDriver;
use framework\database\flcDbResult;
use framework\database\flcDbResultOutParams;
use framework\database\flcDbResults;
use stdClass;

include_once dirname(__FILE__).'/../flcDriver.php';



/**
 * Microsoft sql  Driver Class (based on sqlsrv)
 *
 * This class extends the parent generic driver class: framework\database\driver\flcDriver
 * and is the specific one for ms sql.
 *
 * Important : Part of this class is a modified one of driver class from codeigniter
 * all credits for his authors.
 *
 */
class flcMssqlDriver extends flcDriver {

    /**
     * Hold the connection password
     *
     * @var string|null
     */
    protected ?string $_password = null;


    /**
     * Scrollable flag
     *
     * Determines what cursor type to use when executing queries.
     *
     * FALSE or SQLSRV_CURSOR_FORWARD would increase performance,
     * but would disable num_rows() (and possibly insert_id())
     *
     * @var    mixed
     */
    public $scrollable = null;

    /**
     * The reserved word to execute a stored procedure.
     * @var string
     */
    protected string $_callable_procedure_call_string = 'exec';
    protected array  $_callable_procedure_sep_string  = [' ', ' '];


    // --------------------------------------------------------------------

    protected static array $_cast_conversion = [
        // boolean type
        'boolean' => ['BIT', 'b', 1],
        // text types
        'string' => ['VARCHAR', 't'],
        'char' => ['CHAR', 't'],
        'text' => ['VARCHAR(max)', 't'],
        'nstring' => ['NVARCHAR', 't'],
        'nchar' => ['NCHAR', 't'],
        'ntext' => ['NTEXT', 't'],
        // binary types
        'binary' => ['VARBINARY', 't'],
        'varbinary' => ['VARBINARY', 't'],
        'blob' => ['VARBINARY(max)', 't'],
        // enumerated types
        'enum' => ['', 't'],
        'set' => ['', 't'],
        // Numeric types
        'float' => ['FLOAT', 'n'],
        'double' => ['DOUBLE PRECISION', 'n'],
        'real' => ['REAL', 'n'],
        'decimal' => ['DECIMAL', 'n'],
        'ufloat' => ['FLOAT', 'n'],
        'udouble' => ['DOUBLE PRECISION', 'n'],
        'udecimal' => ['DECIMAL', 'n'],
        'bit' => ['BIT', 'm'],
        'tinyint' => ['SMALLINT', 'n'],
        'utinyint' => ['SMALLINT', 'n'],
        'smallint' => ['SMALLINT', 'n'],
        'usmallint' => ['INTEGER', 'n'],
        'mediumint' => ['INTEGER', 'n'],
        'umediumint' => ['INTEGER', 'n'],
        'int' => ['INTEGER', 'n'],
        'uint' => ['BIGINT', 'n'],
        'bigint' => ['BIGINT', 'n'],
        'ubigint' => ['NUMERIC(20)', 'n'],
        'numeric' => ['NUMERIC', 'n'],
        'unumeric' => ['NUMERIC', 'n'],
        'money' => ['MONEY', 'n'],
        // date / time types
        'date' => ['DATE', 't'],
        'datetime' => ['DATETIME', 't'],
        'timestamp' => ['DATETIME', 't'],
        'time' => ['TIME', 't'],
        'year' => ['SMALLINT', 'n'],
        // json / xml
        'json' => ['VARCHAR', 't'],
        'jsonb' => ['VARCHAR', 't'],
        'xml' => ['XML', 't'],
        //  spatial data types
        'geometry' => ['', 't'],
        'point' => ['', 't'],
        'linestring' => ['', 't'],
        'line' => ['', 't'],
        'lseg' => ['', 't'],
        'path' => ['', 't'],
        'polygon' => ['', 't'],
        'box' => ['', 't'],
        'circle' => ['', 't'],
        'multipoint' => ['', 't'],
        'multilinestring' => ['', 't'],
        'geometrycollection' => ['', 't'],
        // Interval / ranges
        'int4range' => ['', 't'],
        'int8range' => ['', 't'],
        'numrange' => ['', 't'],
        'tsrange' => ['', 't'],
        'tstzrange' => ['', 't'],
        'daterange' => ['', 't'],
        // FULL TEXT SEARCH
        'tsvector' => ['', 't'],
        'tsquery' => ['', 't'],
        //arrays
        'array' => ['', 't'],
        // inet types
        'cidr' => ['', 't'],
        'inet' => ['', 't'],
        'macaddr' => ['', 't'],

    ];

    // --------------------------------------------------------------------

    /**
     * Class constructor
     *
     * @param array|null $p_options if null take defaults
     *
     * @return    void
     */
    public function __construct(?array $p_options = null) {
        parent::__construct($p_options);
        //ini_set('memory_limit','256M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)


        // This is only supported as of SQLSRV 3.0
        // Recommendation left as null , otherwise some functions like get the numrows or isert_id will
        // fail as microsoft document.
        // @link https://docs.microsoft.com/en-us/sql/connect/php/cursor-types-sqlsrv-driver?view=sql-server-ver16
        //
        if ($this->scrollable === null) {
            $this->scrollable = defined('SQLSRV_CURSOR_CLIENT_BUFFERED') ? SQLSRV_CURSOR_CLIENT_BUFFERED : false;
            //$this->scrollable = defined('SQLSRV_CURSOR_STATIC') ? SQLSRV_CURSOR_STATIC : FALSE;
        }
    }

    // --------------------------------------------------------------------

    /**
     * For mssql the dsn prototype will be : 'driver://username:password@hostname/database'
     *
     * @inheritDoc
     */
    public function initialize(?string $p_dsn, ?string $p_host, ?int $p_port, ?string $p_database, ?string $p_user, ?string $p_password, string $p_charset = SQLSRV_ENC_CHAR, string $p_collation = 'SQL_Latin1_General_CP1_CI_AS'): bool {
        // Extract dsn parts if well defined, if the values are on dsn they are taken otherwise extract
        // them from the parameters

        // preserve the charset
        if ($p_charset) {
            $this->_charset = in_array(strtolower($p_charset), [
                'utf-8',
                'utf8'
            ], true) ? 'UTF-8' : $p_charset;
        } else {
            $this->_charset = SQLSRV_ENC_CHAR;
        }

        /* if ($charset == 'UTF-8' && isset($p_collation)) {
             substr($p_collation, -strlen($needle))===$needle
         }*/

        $query = '';
        if (($parsedDsn = @parse_url($p_dsn)) !== false) {
            $p_host = (isset($parsedDsn['host']) ? rawurldecode($parsedDsn['host']) : $p_host);
            $p_port = (isset($parsedDsn['port']) ? rawurldecode($parsedDsn['port']) : $p_port);
            $p_user = (isset($parsedDsn['user']) ? rawurldecode($parsedDsn['user']) : $p_user);
            $p_password = (isset($parsedDsn['pass']) ? rawurldecode($parsedDsn['pass']) : $p_password);
            $p_database = (isset($parsedDsn['database']) ? rawurldecode($parsedDsn['database']) : $p_database);
            $query = isset($parsedDsn['query']) ? rawurldecode($parsedDsn['query']) : "";

        }


        // Set default if values not defined , generate the full dsn for postgres
        $p_port = ($p_port ?? '1433');

        // if not user and password defined , try as a windows credentials login.
        if (!isset($p_user) && !isset($p_password)) {
            $p_user = '';
            $p_password = '';
        }

        // Check values
        if (!isset($p_host) || !isset($p_database)) {
            return false;
        } else {
            $this->_password = $p_password;

            $this->_dsn = 'mssql://'.$p_user.':'.$p_password.'@'.$p_host.':'.$p_port.'/'.$p_database;
            if ($query && $query != "") {
                $this->_dsn .= '&'.$query;
            }
        }

        // preserve the collation
        if ($p_collation) {
            $this->_collation = $p_collation;
        }


        return true;
    }
    // --------------------------------------------------------------------


    /**
     * @inheritdoc
     */
    protected function _open($p_pooling = false) {
        $conn_info = [
            'UID' => $this->get_user(),
            'PWD' => $this->_password,
            'Database' => $this->get_database(),
            'ConnectionPooling' => ($p_pooling === true) ? 1 : 0,
            'CharacterSet' => $this->_charset,
            'Encrypt' => ($this->encrypt === true) ? 1 : 0,
            'ReturnDatesAsStrings' => 1
        ];

        // If the username and password are both empty, assume this is a
        // 'Windows Authentication Mode' connection.
        if (empty($conn_info['UID']) && empty($conn_info['PWD'])) {
            unset($conn_info['UID'], $conn_info['PWD']);
        }

        if (false !== ($conn = sqlsrv_connect($this->get_host(), $conn_info))) {
            // Determine how identifiers are escaped
            $query = sqlsrv_query($conn, 'SELECT CASE WHEN (@@OPTIONS | 256) = @@OPTIONS THEN 1 ELSE 0 END AS qi');
            $rows = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC);
            $quoted_identifier = empty($rows) ? false : (bool)$rows['qi'];
            $this->_escape_char = ($quoted_identifier) ? '"' : [
                '[',
                ']'
            ];

            sqlsrv_free_stmt($query);
            unset($query);
        }

        return $conn;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _close(): void {
        sqlsrv_close($this->_connId);
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _set_charset(string $p_charset): bool {
        // Not supported only can be set on the connection.
        return false;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function get_db_driver(): string {
        return 'mssql';
    }

    // --------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function connect(): bool {
        return $this->open();
    }

    // --------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function select_database(string $p_database): bool {
        if ($p_database === '') {
            $p_database = $this->get_database();
        }

        if (($res = $this->_execute_qry('USE '.$this->escape_identifiers($p_database)))) {
            sqlsrv_free_stmt($res);
            unset($res);

            $this->_set_database($p_database);

            return true;
        }

        $this->display_error("Select database for $p_database fail", 'E');

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function error(): array {
        $error = [
            'code' => '00000',
            'message' => ''
        ];
        $sqlsrv_errors = sqlsrv_errors(SQLSRV_ERR_ERRORS);

        if (!is_array($sqlsrv_errors)) {
            return $error;
        }

        $sqlsrv_error = array_shift($sqlsrv_errors);
        if (isset($sqlsrv_error['SQLSTATE'])) {
            $error['code'] = isset($sqlsrv_error['code']) ? $sqlsrv_error['SQLSTATE'].'/'.$sqlsrv_error['code'] : $sqlsrv_error['SQLSTATE'];
        } elseif (isset($sqlsrv_error['code'])) {
            $error['code'] = $sqlsrv_error['code'];
        }

        if (isset($sqlsrv_error['message'])) {
            $error['message'] = $sqlsrv_error['message'];
        }

        return $error;
    }


    // --------------------------------------------------------------------

    /**
     * IMPORTANT : If you are calling an stored procedure and need to get the error
     * generated by a THROW or RAISERROR and return false , always begin the sp
     * with SET NOCOUNT ON  !!!!!
     *
     * @inheritdoc
     */
    protected function _execute_qry(string $p_sqlquery) {
        return ($this->scrollable === false or $this->is_write_type($p_sqlquery)) ? sqlsrv_query($this->_connId, $p_sqlquery) : sqlsrv_query($this->_connId, $p_sqlquery, null, [
            'Scrollable' => $this->scrollable,
            'SendStreamParamsAtExec' => 0
        ]);
    }

    // --------------------------------------------------------------------

    /***************************************************************
     * Metadata
     */

    /**
     * @inheritdoc
     */
    protected function _list_columns_qry(string $p_table = ''): string {
        return 'SELECT COLUMN_NAME as column_name
			FROM information_schema.Columns
			WHERE UPPER(TABLE_NAME) = '.$this->escape(strtoupper($p_table));
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function primary_key(string $p_table): ?string {
        // MSSQL way to obtain the primary key field name
        $qry = "select C.COLUMN_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS T
                    JOIN INFORMATION_SCHEMA.CONSTRAINT_COLUMN_USAGE C ON C.CONSTRAINT_NAME=T.CONSTRAINT_NAME
                WHERE
                C.TABLE_NAME=".$this->escape($p_table)." and T.CONSTRAINT_TYPE='PRIMARY KEY';";

        $RES = $this->execute_query($qry);

        if ($RES && $RES->num_rows() > 0) {
            $colname = $RES->first_row()->COLUMN_NAME;
            $RES->free_result();

            return $colname;
        } else {
            return null;
        }

    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _column_data_qry(string $p_table): string {
        return 'SELECT  * FROM '.$p_table.' LIMIT 1';
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function column_data(string $p_table, string $p_schema = ''): ?array {
        if ($p_schema == '') {
            $schema = 'dbo';
        } else {
            $schema = $p_schema;
        }

        $sql = 'SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, NUMERIC_PRECISION, COLUMN_DEFAULT
			FROM INFORMATION_SCHEMA.Columns
			WHERE TABLE_SCHEMA = '.$this->escape($schema).' and UPPER(TABLE_NAME) = '.$this->escape(strtoupper($p_table));

        if (($query = $this->execute_query($sql)) === null) {
            return null;
        }
        $ans = $query->result_object();

        $retval = [];
        for ($i = 0, $c = count($ans); $i < $c; $i++) {
            $retval[$i] = new stdClass();
            $retval[$i]->name = $ans[$i]->COLUMN_NAME;
            $retval[$i]->type = $ans[$i]->DATA_TYPE;
            $retval[$i]->max_length = ($ans[$i]->CHARACTER_MAXIMUM_LENGTH > 0) ? $ans[$i]->CHARACTER_MAXIMUM_LENGTH : $ans[$i]->NUMERIC_PRECISION;
            $retval[$i]->default = $ans[$i]->COLUMN_DEFAULT;
        }

        $query->free_result();

        return $retval;
    }

    /**
     * @inheritdoc
     */
    protected function _list_tables_qry(string $p_schema, string $p_table_constraints = ''): string {
        // if not set default to dbo.
        if (!isset($p_schema) || trim($p_schema) == '') {
            $p_schema = 'dbo';
        }

        $sql = "SELECT  TABLE_NAME as table_name FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=".$this->escape($p_schema);

        if (isset($p_table_constraints) && $p_table_constraints !== '') {
            $sql .= ' AND TABLE_NAME LIKE \'%'.$p_table_constraints.'%\'';
        }

        return $sql;
    }

    // --------------------------------------------------------------------

    /*************************************************************
     * Helpers
     */

    /**
     * @inheritdoc
     */
    protected function _version_qry(): string {
        return 'SELECT @@VERSION AS ver';
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    function escape_identifiers($p_item) {
        if (!isset($p_item) or empty($p_item)) {
            return '';
        }

        if (is_numeric($p_item)) {
            return $p_item;
        }

        $non_displayables = [
            '/%0[0-8bcef]/',        // URL encoded 00-08, 11, 12, 14, 15
            '/%1[0-9a-f]/',         // url encoded 16-31
            '/[\x00-\x08]/',        // 00-08
            '/\x0b/',               // 11
            '/\x0c/',               // 12
            '/[\x0e-\x1f]/',        // 14-31
            '/\27/'
        ];
        foreach ($non_displayables as $regex) {
            $p_item = preg_replace($regex, '', $p_item);
        }
        $replace = ['"', "'", '='];

        return str_replace($replace, "*", $p_item);
    }
    // --------------------------------------------------------------------

    /***********************************************************************
     * Transaction managing
     */

    /**
     * @inheritdoc
     */
    protected function _trans_begin(): bool {
        return sqlsrv_begin_transaction($this->_connId);
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _trans_commit(): bool {
        return sqlsrv_commit($this->_connId);
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _trans_rollback(string $p_savepoint = ''): bool {
        if ($p_savepoint === '') {
            return sqlsrv_rollback($this->_connId);
        } else {
            return (bool)sqlsrv_query($this->_connId, 'ROLLBACK TRANSACTION '.$p_savepoint.';');
        }
    }

    // --------------------------------------------------------------------

    /***
     * @inheritdoc
     */
    protected function _trans_savepoint(string $p_savepoint): bool {
        $query = 'SAVE TRANSACTION '.$p_savepoint.';';
        echo $query.PHP_EOL;

        return (bool)sqlsrv_query($this->_connId, $query);
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _trans_remove_savepoint(string $p_savepoint): bool {
        // NOT SUPPORTED IN T_SQL always retur tru to allow normal flow of the driver
        return true;
    }

    // --------------------------------------------------------------------
    /*******************************************************
     * DB dependant methods
     */

    /**
     *
     * @inheritdoc
     *
     * Important : SQL SERVER doesnt allow order by on updates.
     */
    protected function _update(string $p_table, array $p_values, string $p_where, int $p_limit = 0, ?string $p_orderby = null): string {
        $valstr = [];
        foreach ($p_values as $key => $val) {
            $valstr[] = $key.' = '.$val;
        }

        $limitstr = '';
        if ($p_limit > 0) {
            $limitstr = "TOP($p_limit)";
        }

        return "UPDATE $limitstr $p_table  SET ".implode(', ', $valstr)." WHERE $p_where";
    }

    // --------------------------------------------------------------------

    /***********************************************************************
     * Stored procedures and function support
     */

    /**
     * @inheritdoc
     */
    public function execute_function(string $p_fn_name, ?array $p_parameters = null, ?array $p_casts = null): ?flcDbResult {

        $sqlinfo = /** @lang TSQL */
            'SELECT sm.object_id,
                           OBJECT_NAME(sm.object_id) AS object_name,
                           o.type,
                           o.type_desc,
                           sm.definition,
                           sm.uses_ansi_nulls,
                           sm.uses_quoted_identifier,
                           sm.is_schema_bound,
                           sm.execute_as_principal_id
                        FROM sys.sql_modules AS sm
                        JOIN sys.objects AS o ON sm.object_id = o.object_id
                        WHERE sm.object_id = OBJECT_ID(\''.$p_fn_name.'\')
                        ORDER BY o.type;';

        $type = null;
        if ($res = $this->_execute_qry($sqlinfo)) {
            $type = sqlsrv_fetch_array($res)['type_desc'];
            sqlsrv_free_stmt($res);
            unset($res);
        } else {
            $this->display_error("execute function $p_fn_name fail", 'E');

            return null;
        }

        if ($type && $type == 'SQL_SCALAR_FUNCTION') {
            $sqlfunc = $this->callable_string($p_fn_name, 'function', 'scalar', $p_parameters, $p_casts);
        } else {
            $sqlfunc = $this->callable_string($p_fn_name, 'function', 'records', $p_parameters, $p_casts);

        }
        echo $sqlfunc.PHP_EOL;

        return $this->execute_query($sqlfunc);

    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function execute_stored_procedure(string $p_fn_name, string $p_type, ?array $p_parameters = null, ?array $p_casts = null): ?flcDbResults {

        $params = [];
        $oparams = [];
        $outparams_count = 0;
        $sqlpre = '';
        $sqlpost = '';
        $is_outparams = false;

        if ($p_parameters && count($p_parameters) > 0) {
            for ($i = 0; $i < count($p_parameters); $i++) {
                // determine if parameter is an array or simple and get the value.
                if (is_array($p_parameters[$i])) {
                    $sqlpre = '';
                    $sqlpost = '';

                    // take the parameters descriptors.
                    $value = $p_parameters[$i][0];
                    $paramtype = $p_parameters[$i][1] ?? '';
                    $sqltype = $p_parameters[$i][2] ?? '';

                    // Ignore refcursors for mantain compatability with pgsql
                    if ($sqltype == 'refcursor') {
                        continue;
                    }

                    if ($p_type == self::FLCDRIVER_PROCTYPE_SCALAR) {
                        // generate pre and post for catch the scalar return
                        $sqlpre .= 'DECLARE @p'.$i.' '.$sqltype.';';
                        $sqlpost .= 'SELECT @p'.$i.' as p'.$i.';';
                        $params[] = $value;
                    } elseif ($paramtype == self::FLCDRIVER_PARAMTYPE_OUT || $paramtype == self::FLCDRIVER_PARAMTYPE_INOUT) {
                        $is_outparams = true;

                        // In mssql server al output parameters are input also , no specific code for each case here.
                        ${'p'.$outparams_count} = $value;

                        $oparams[] = [
                            &${'p'.$outparams_count},
                            $paramtype == self::FLCDRIVER_PARAMTYPE_INOUT ? SQLSRV_PARAM_INOUT : SQLSRV_PARAM_OUT
                        ];
                        $params[] = '?';
                        $outparams_count++;
                    } else {
                        $params[] = $value;
                    }


                } else {
                    $params[] = (is_string($p_parameters[$i]) ? '\''.$p_parameters[$i].'\'' : $p_parameters[$i]);
                }
            }
        }

        // create sp store results
        require_once(dirname(__FILE__).'/../../flcDbResults.php');
        $results = new flcDbResults();


        // Get the callable string
        // No cast allowed in mssql server on stored procedures calls, ignore the parameter
        $sqlfunc = $this->callable_string($p_fn_name, 'procedure', 'records', $params);


        // Process the stored procedurre
        //

        $is_resultset = ($p_type == self::FLCDRIVER_PROCTYPE_RESULTSET);
        $is_multiresultset = ($p_type == self::FLCDRIVER_PROCTYPE_MULTIRESULTSET);

        // If only a sp with a single return value?
        // generate the sql code and execute
        if ($p_type == self::FLCDRIVER_PROCTYPE_SCALAR) {
            $sqlfunc = $sqlpre.$sqlfunc.$sqlpost;
            $sqlfunc = str_replace($this->_callable_procedure_call_string.' ', $this->_callable_procedure_call_string.' @p0 = ', $sqlfunc);
            echo $sqlfunc.PHP_EOL;

            if ($res = $this->execute_query($sqlfunc)) {
                $results->add_resultset_result($res);
            } else {
                $this->display_error("execute stored procedure $p_fn_name fail", 'E');
            }

        } // For stores procedures with output parameters o resultset or both
        elseif ($is_outparams || $is_resultset || $is_multiresultset) {
            echo $sqlfunc.PHP_EOL;


            // execute
            $res = sqlsrv_query($this->_connId, $sqlfunc, $oparams);
            if ($res) {
                // If a resulset type sp , get the results.
                if ($is_resultset || $is_multiresultset) {

                    // In single resultsets , we don prefetch the answers , is not required and also is more
                    // efficient.
                    if (!$is_outparams && $is_resultset) {
                        $result_driver = $this->load_result_driver();
                        $result = new $result_driver($this, $res);

                        $results->add_resultset_result($result);

                    } else {
                        // Get the resulset or resultsets
                        do {
                            $result_driver = $this->load_result_driver();
                            $result = new $result_driver($this, $res);

                            // Yes , we need to pre-fetch al records , inefficient yes , but required in case we have to fetch the output parametes.
                            // Info on docs : "make sure all result sets are stepped through,  since the output params may not be set until this happens"
                            // Also we can never get the next resulset results without prefetching the previous ones.
                            // Bad , bad , but no other way.
                            $result->result_array();

                            $results->add_resultset_result($result);
                        } while (sqlsrv_next_result($res));

                    }
                }

                // If we ned to process output params?
                if ($is_outparams) {
                    require_once(dirname(__FILE__).'/../../flcDbResultOutParams.php');

                    $outparams = new flcDbResultOutParams();

                    if (isset($oparams) && count($oparams) > 0) {
                        for ($i = 0; $i < count($oparams); $i++) {
                            $outparams->add_out_param('p'.$i, ${'p'.$i});
                        }

                    }

                    $results->add_outparams_result($outparams);
                }


            } else {
                $this->display_error("execute stored procedure $p_fn_name fail", 'E');

                return null;
            }

        }

        return $results;

    }
}