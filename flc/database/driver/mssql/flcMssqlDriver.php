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
use flc\database\flcDbResultOutParams;
use flc\database\flcDbResults;
use Throwable;


/**
 * Microsoft sql  Driver Class (based on sqlsrv)
 *
 * This class extends the parent generic driver class: flc\database\driver\flcDriver
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


    /**
     * Flag to indicate is casts on sp or function are supported
     * Some db like mssql doesnt support casts in callables
     * @var bool
     */
    protected bool $_cast_on_callables = false;


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
            $quoted_identifier = !empty($rows) && (bool)$rows['qi'];
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
    public function set_charset(string $p_charset): bool {
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

        $this->log_error("Select database for $p_database fail", 'E');

        return false;
    }

    /**
     * @inheritdoc
     */
    public function get_limit_offset_str(int $p_start_row, int $p_end_row): string {
        if ($p_end_row - $p_start_row <= 0) {
            return '';
        }

        return 'offset '.$p_start_row.' rows  fetch next '.($p_end_row - $p_start_row).' rows only ';
    }


    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function error(): array {
        $error = [
            'code' => 0,
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
    public function primary_key(string $p_table): array {
        $pkeys = [];
        // MSSQL way to obtain the primary key field name
        $qry = "select C.COLUMN_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS T
                    JOIN INFORMATION_SCHEMA.CONSTRAINT_COLUMN_USAGE C ON C.CONSTRAINT_NAME=T.CONSTRAINT_NAME
                WHERE
                C.TABLE_NAME=".$this->escape($p_table)." and T.CONSTRAINT_TYPE='PRIMARY KEY';";

        $RES = $this->execute_query($qry);

        if ($RES && $RES->num_rows() > 0) {
            $rows = $RES->result_array();
            foreach ($rows as $row) {
                $pkeys[] = $row['COLUMN_NAME'];
            }
            $RES->free_result();

        }
        return $pkeys;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _column_data_qry(string $p_table,?string $p_schema = null): string {
        return "select col_name, col_type, case when charindex('(',col_definition) > 0 then col_length else null end as col_length, col_scale,col_definition,col_default,col_is_pkey,col_is_nullable
                FROM(
                        SELECT I.COLUMN_NAME as col_name, DATA_TYPE as col_type, coalesce(CHARACTER_MAXIMUM_LENGTH, NUMERIC_PRECISION) as col_length,NUMERIC_SCALE as col_scale,
                               DATA_TYPE +
                               CASE
                                   --types without length, precision, or scale specifiecation
                                   WHEN DATA_TYPE IN (N'int', N'bigint', N'smallint', N'tinyint', N'money', N'smallmoney', N'real', N'datetime',
                                                      N'smalldatetime', N'bit', N'image', N'text', N'uniqueidentifier', N'date', N'ntext',
                                                      N'sql_variant', N'hierarchyid', 'geography', N'timestamp', N'xml')
                                       THEN N''
                                   --types with precision and scale specification
                                   WHEN DATA_TYPE in (N'decimal', N'numeric')
                                       THEN N'(' + CAST(NUMERIC_PRECISION AS varchar(5)) + N',' + CAST(NUMERIC_SCALE AS varchar(5)) + N')'
                                   --types with scale specification only
                                   WHEN DATA_TYPE in (N'time', N'datetime2', N'datetimeoffset')
                                       THEN N'(' + CAST(NUMERIC_SCALE AS varchar(5)) + N')'
                                   --float default precision is 53 - add precision when column has a different precision value
                                   WHEN DATA_TYPE in (N'float')
                                       THEN CASE
                                                WHEN NUMERIC_PRECISION = 53 THEN N''
                                                ELSE N'(' + CAST(NUMERIC_PRECISION AS varchar(5)) + N')' END
                                   --types with length specifiecation
                                   ELSE N'(' + CASE CHARACTER_MAXIMUM_LENGTH
                                                   WHEN -1 THEN N'MAX'
                                                   ELSE CAST(CHARACTER_MAXIMUM_LENGTH AS nvarchar(20)) END + N')'
                                   END                                                            as col_definition,
                               CASE
                                   when left(COLUMN_DEFAULT, 2) = '((' then
                                       REPLACE(SUBSTRING(COLUMN_DEFAULT, 3, LEN(COLUMN_DEFAULT) - 4), '''', '')
                                   else
                                       case
                                           when left(COLUMN_DEFAULT, 1) = '(' then
                                                   STUFF(SUBSTRING(COLUMN_DEFAULT, 1, 1), 1, 1, '') + SUBSTRING(COLUMN_DEFAULT, 2, LEN(COLUMN_DEFAULT) - 2) +
                                                   STUFF(SUBSTRING(COLUMN_DEFAULT, LEN(COLUMN_DEFAULT), 1), 1, 1, '')
                                           else
                                               COLUMN_DEFAULT
                                           end
                                   end                                                            as col_default,
                               case when COALESCE(C.COLUMN_NAME, NULL) IS NULL THEN 0 ELSE 1 end  as col_is_pkey,
                               case when IS_NULLABLE = 'YES' THEN 1 else 0 end as col_is_nullable
                    FROM INFORMATION_SCHEMA.Columns I
                    LEFT JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS T
                                   ON T.TABLE_CATALOG = I.TABLE_CATALOG and T.TABLE_SCHEMA = I.TABLE_SCHEMA and
                                      T.TABLE_NAME = I.TABLE_NAME
                    LEFT JOIN INFORMATION_SCHEMA.CONSTRAINT_COLUMN_USAGE C
                                   ON C.CONSTRAINT_NAME = T.CONSTRAINT_NAME AND C.COLUMN_NAME = I.COLUMN_NAME
                    WHERE I.TABLE_SCHEMA = ".$this->escape($p_schema).
            ' and UPPER(I.TABLE_NAME) = '.$this->escape(strtoupper($p_table)).
            ') res';
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

        return parent::column_data($p_table,$schema);

    }

    // --------------------------------------------------------------------

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
     *
     */
    public function cast_to_rowversion($p_value) {
        // in ms sql server is a timestamp , this si the way to convert
        return '0x'.bin2hex($p_value);
    }


    /**
     * @inheritdoc
     */
    protected function _version_qry(): string {
        return "SELECT CONVERT(VARCHAR(128), SERVERPROPERTY ('productversion')) AS ver";
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

        // echo $query.PHP_EOL;

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

        if ($res = $this->_execute_qry($sqlinfo)) {
            $type = sqlsrv_fetch_array($res)['type_desc'];
            sqlsrv_free_stmt($res);
            unset($res);
        } else {
            $this->log_error("execute function $p_fn_name fail", 'E');

            return null;
        }

        if (!empty($this->dbprefix)) {
            $p_fn_name = $this->dbprefix.$p_fn_name;
        }

        if ($type && $type == 'SQL_SCALAR_FUNCTION') {
            $sqlfunc = $this->callable_string($p_fn_name, 'function', 'scalar', $p_parameters, $p_casts);
        } else {
            $sqlfunc = $this->callable_string($p_fn_name, 'function', 'records', $p_parameters, $p_casts);

        }

        // echo $sqlfunc.PHP_EOL;

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

                    // take the parameters descriptors.
                    $value = $p_parameters[$i][0];
                    $paramtype = $p_parameters[$i][1] ?? '';
                    $sqltype = $p_parameters[$i][2] ?? '';

                    // Ignore refcursors for mantain compatability with pgsql
                    if ($sqltype == 'refcursor') {
                        continue;
                    }


                    if ($p_type == self::FLCDRIVER_PROCTYPE_SCALAR && $sqlpre == '') {
                        // generate pre and post for catch the scalar return
                        $sqlpre .= 'DECLARE @p0 '.$sqltype.';';
                        $sqlpost .= 'SELECT @p0 as p0;';
                        $params[] = $value;
                    } elseif ($paramtype == self::FLCDRIVER_PARAMTYPE_OUT || $paramtype == self::FLCDRIVER_PARAMTYPE_INOUT) {
                        $is_outparams = true;

                        // In mssql server al output parameters are input also , no specific code for each case here.
                        ${'p'.$outparams_count} = $value;

                        $oparams[] = [
                            &${'p'.$outparams_count},
                            $paramtype == self::FLCDRIVER_PARAMTYPE_INOUT ? SQLSRV_PARAM_INOUT : SQLSRV_PARAM_OUT
                        ];
                        $params[] = '<outparam=?>';
                        $outparams_count++;
                    } elseif ($paramtype == 'readonly') {
                        $is_outparams = true;

                        ${'p'.$outparams_count} = [$sqltype => $value];

                        // If this is a readonly parameter in mssql allways is a table value
                        // parameter
                        $oparams[] = [
                            &${'p'.$outparams_count},
                            SQLSRV_PARAM_IN,
                            SQLSRV_PHPTYPE_TABLE,
                            SQLSRV_SQLTYPE_TABLE
                        ];

                        // need to be binded.
                        $params[] = '<outparam=?>';
                        $outparams_count++;

                    } else {
                        $params[] = $value;
                    }


                } else {
                    $params[] = $p_parameters[$i];

                    //$params[] = (is_string($p_parameters[$i]) ? '\''.$p_parameters[$i].'\'' : $p_parameters[$i]);
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
            // echo $sqlfunc.PHP_EOL;

            if ($res = $this->execute_query($sqlfunc)) {
                $results->add_resultset_result($res);
            } else {
                $this->log_error("execute stored procedure $p_fn_name fail", 'E');
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
                $this->log_error("execute stored procedure $p_fn_name fail", 'E');

                return null;
            }

        }

        return $results;

    }

    // --------------------------------------------------------------------


    /**
     * @inheritdoc
     */
    public function execute_callable(string $p_callable_name, string $p_type, ?array $p_params_values = null, ?array $p_exclude_casts = null): ?flcDbResults {
        $params = [];
        $oparams = [];
        // $outparams_count = 0;
        $sqlpre = '';
        $sqlpost = '';
        $is_outparams = false;

        $callable_type = $this->_get_callable_type($p_callable_name);

        if ($callable_type == null) {
            return null;
        }

        // parameter analisys
        $parameter_descriptors = $this->get_callable_parameter_types($p_callable_name, $callable_type == 'p' ? 'procedure' : 'function');

        // if it its a function , prepare neccesary function parameters and execute
        if ($callable_type === 'f') {
            $func_parameters = [];
            if ($parameter_descriptors !== null) {
                foreach ($parameter_descriptors as $param_name => $parameter_descriptor) {

                    // sql server no require param direction , allways is input , only values required
                    $func_parameters[] = [$p_params_values[$param_name] ?? null];
                }
            }

            // create sp store results
            require_once(dirname(__FILE__).'/../../flcDbResults.php');
            $results = new flcDbResults();

            $res = $this->execute_function($p_callable_name, $func_parameters);
            if ($res) {
                $results->add_resultset_result($res);
                return $results;
            } else {
                $this->log_error("execute stored procedure extended $p_callable_name fail", 'E');
                return null;
            }

        }

        // Ok its a procedure.
        if ($parameter_descriptors !== null) {

            foreach ($parameter_descriptors as $param_name => $parameter_descriptor) {
                $is_outparams = false;

                // get the descriptors of the parameter
                // the form is : [param1=> ['varchar','INOUT'],param2=>['int','IN'],param3=>['varchar','IN','(20)']]
                $sqltype = $parameter_descriptor[0];
                $paramtype = $parameter_descriptor[1];
                $appendstr = $parameter_descriptor[2] ?? '';
                // if not value defined , then try default value , if not exist then null value
                $value = $p_params_values[$param_name] ?? ($parameter_descriptor[3] ?? null);


                if ($p_type == self::FLCDRIVER_PROCTYPE_SCALAR && $sqlpre == '') {
                    // generate pre and post for catch the scalar return
                    $sqlpre .= 'DECLARE @p0 '.$sqltype.';';
                    $sqlpost .= 'SELECT @p0 as p0;';
                    $params[$param_name] = $value;
                } elseif ($paramtype == 'out' || $paramtype == 'inout') {
                    // In mssql server al output parameters are input also , no specific code for each case here.
                    $is_outparams = true;

                    // remove the @ at the beggining , because php doesnt allow for define a
                    // variable.
                    $param_ext = substr($param_name, 1);

                    // sqlserver not allow null values on inout parameter , its requiered
                    // at least a default value based on his php type.
                    // this can happen when an output parameter its not defined in the list
                    // $p_params_values.
                    if ($value === null) {
                        $ntype = $this->get_normalized_type($sqltype);
                        if ($ntype == 'string') {
                            $$param_ext = '';
                        } else {
                            $$param_ext = 0;
                        }

                    } else {
                        $$param_ext = $value;
                    }

                    $oparams[] = [
                        &$$param_ext,
                        SQLSRV_PARAM_INOUT
                    ];

                    $params[$param_name] = '?';

                } elseif ($paramtype == 'readonly') {
                    $is_outparams = true;

                    // remove the @ at the beggining , because php doesnt allow for define a
                    // variable.
                    $param_ext = substr($param_name, 1);
                    $$param_ext = [$sqltype => $value];

                    // If this is a readonly parameter in mssql allways is a table value
                    // parameter
                    $oparams[] = [
                        $$param_ext,
                        SQLSRV_PARAM_IN,
                        SQLSRV_PHPTYPE_TABLE,
                        SQLSRV_SQLTYPE_TABLE
                    ];

                    // need to be binded.
                    $params[$param_name] = '?';

                } else {
                    $params[$param_name] = $value;
                }

            }
        }
        // create sp store results
        require_once(dirname(__FILE__).'/../../flcDbResults.php');
        $results = new flcDbResults();


        // Get the callable string
        // No cast allowed in mssql server on stored procedures calls, ignore the parameter
        $sqlfunc = $this->callable_string_extended($p_callable_name, 'procedure', 'records', $params, $parameter_descriptors);
        echo $sqlfunc.PHP_EOL;

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
                $this->log_error("execute stored procedure extended $p_callable_name fail", 'E');
            }

        } // For stores procedures with output parameters o resultset or both
        elseif ($is_outparams || $is_resultset || $is_multiresultset) {
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

                    foreach ($parameter_descriptors as $param_name => $parameter_descriptor) {
                        if ($parameter_descriptor[1] != 'in') {
                            $param_ext = substr($param_name, 1); // remove @
                            $outparams->add_out_param($param_ext, $$param_ext);
                        }
                    }

                    $results->add_outparams_result($outparams);
                }


            } else {
                $this->log_error("execute stored procedure extended $p_callable_name fail", 'E');

                return null;
            }

        }

        return $results;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function get_callable_parameter_types(string $p_callable_name, string $p_callable_type): ?array {
        // if is a procedure the type is 'P' , if is a function type need to be in ('FN','AF','FS','FT','IF','TF')
        $sql_where_to_append = "o.type = 'P'";
        if ($p_callable_type != 'procedure') {
            $sql_where_to_append = "o.type in ('FN','AF','FS','FT','IF','TF')";
        }

        $sql = "
            -- final para codigo
            -- SOLUTION
            with def_params(type, name, definition, have_parentesis)
                     as (select o.type
                              , o.name as procname
                              , ltrim(substring(
                            replace(replace(replace(replace(sm.definition, char(10), ' '), char(13), ' '), char(9), ' '),  'FOR REPLICATION', ''), charindex('@', sm.definition), 10000))
                                       as definition
                              -- the function or sp definition contains parenthesis ?=> fn(............) or [fn](............)
                              -- check
                              , case
                                    when
                                            charindex(o.name + '(',
                                                      replace(replace(replace(replace(sm.definition, char(10), ' '), char(13), ' '), char(9), ' '), ' ', '') ) > 0
                                        then  1
                                    when
                                            charindex(o.name + '](',
                                                      replace(replace(replace(replace(sm.definition, char(10), ' '), char(13), ' '),  char(9), ' '), ' ', '') ) > 0
                                        then 1
                                    else 0
                        end  as have_parentesis
                         from sys.sql_modules sm WITH (NOLOCK)
                                  JOIN sys.objects o WITH (NOLOCK) ON sm.[object_id] = o.[object_id]
                                  JOIN sys.schemas s  WITH (NOLOCK) ON o.[schema_id] = s.[schema_id]
                    where o.name = '$p_callable_name' and $sql_where_to_append 
                )
            select
                  sm2.name
                 -- Remove definitions like @int NOT NULL OUT or @int NULL OUT
                 -- for example.
                 ,REPLACE(REPLACE(case
                                      --  No @ means no parameters
                                      when charindex('@', sm2.params) <= 0
                                          then ''
                                      -- remove a trailing right parenthesis
                                      -- and also NOT NULL and NULL from the parameter definition because is useless for my purposes.
                                      when sm2.have_parentesis = 1
                                          then reverse(SUBSTRING(REVERSE(rtrim(sm2.params)), 2, 9999))
                                      else
                                          sm2.params
                                      end ,' NOT NULL ',' '),' NULL ',' ') as args
            FROM (SELECT
                      type
                       , definition
                       , have_parentesis
                       , name
                       -- Remove first RETURNS , WITH or AS in that order and then remove that part
                       -- to left only the parameter list.
                       , case
                             when CHARINDEX(' RETURNS ', definition) > 0 then
                                 SUBSTRING(definition, 0,
                                           CHARINDEX(' RETURNS ', definition))
                             when CHARINDEX(' WITH ', definition) > 0 then
                                 SUBSTRING(definition, 0,
                                           CHARINDEX(' WITH ', definition))
                             else
                                 SUBSTRING(definition, 0,
                                           CHARINDEX(' AS ', definition))
                    end as params
                  FROM def_params) sm2";

        $res = $this->execute_query($sql);
        if ($res) {
            $answers = [];

            $numrows = $res->num_rows();

            if ($numrows > 0) {
                if ($numrows == 1) {
                    $row = $res->row();
                    $param_descriptor = $row->args;
                    echo $param_descriptor.PHP_EOL;

                    // separate  each parameter descriptor
                    $rowfields = array_map('trim', explode(',', $param_descriptor, 100));
                    $rowfields_parsed = [];

                    // sometimes like numeric(10,2) , this descriptor will be treated as two beacuse of the comma,
                    // here we detect the anomaly and join.
                    for ($i = 0; $i < count($rowfields); $i++) {

                        // if not beggining in a parameter , we foun a breaked descriptor
                        if (substr(trim($rowfields[$i]), 0, 1) != '@') {
                            // join
                            $rowfields_parsed[] = $rowfields[$i - 1].','.$rowfields[$i];
                            continue;
                        }


                        $rowfields_parsed[] = $rowfields[$i];
                    }

                    // separate each descriptor on his parts
                    foreach ($rowfields_parsed as $element) {
                        $varfound = true;
                        $vardefault = '';
                        $varsqltype = '';
                        $varname = '';
                        $appendstr = '';
                        $vardirection = 'in';


                        // separate each elemen parts
                        $parts = array_map('trim', explode(' ', $element, 3));

                        if (count($parts) == 2) {
                            // example
                            // @mes int, ......
                            $varname = $parts[0];
                            $varsqltype = $parts[1];
                        } else {
                            if (count($parts) >= 3) {
                                //  example
                                // @p1 varchar(100) = 'test' out,
                                $varname = $parts[0];
                                $varsqltype = $parts[1];

                                if (count($parts) == 3) {
                                    //  example
                                    // @p1 varchar(100) = 'test'
                                    // @p1 varchar(100) out

                                    if (substr($parts[2], 0, 1) == '=') {

                                        $vardefault = $parts[2];
                                        // the direction is 'in' because is not defined

                                    } else {
                                        // not default , then the third element is the direction
                                        $vardirection = strtolower($parts[2]);
                                    }
                                } else {
                                    $vardefault = $parts[2];
                                    $vardirection = strtolower($parts[3]);

                                }

                            } else {
                                // otherwise no parameters
                                $varfound = false;
                            }

                        }
                        // if a var is found , process the var declaration part
                        if ($varfound) {
                            // parse the parameter to extract

                            // for use case as @var(20)='x'
                            // first extract the default value
                            $vardefault_parts = explode('=', $vardefault);
                            if (isset($vardefault_parts[1])) {
                                $vardefault = trim($vardefault_parts[1]);
                            }

                            // now check if have append string ( char(20) means (20) is tha append string
                            $varsqltype_parts = explode('(', $varsqltype);
                            if (isset($varsqltype_parts[1])) {
                                $appendstr = '('.$varsqltype_parts[1];
                            }
                            $varsqltype = $varsqltype_parts[0];


                            // generate the descriptor entry.
                            $answers[$varname] = [
                                $varsqltype,
                                $vardirection == 'output' ? 'out' : $vardirection,
                                $appendstr
                            ];
                            if ($vardefault !== null) {
                                $answers[$varname][] = $vardefault;
                            }
                        }
                    }


                } else {
                    $answers = null;

                    // log an error and return null , more than one funtion/stored procedure with the same name is
                    // not allowed.                }
                    $this->log_error('get_callable_parameter_types - The existence of one sp or function with the same name is not allowed', 'e');
                }
            } else {
                $answers = null;

                // log an error and return null , more than one funtion/stored procedure with the same name is
                // not allowed.                }
                $this->log_error("get_callable_parameter_types - sp/function $p_callable_name doesnt exist", 'e');
            }

            $res->free_result();

        } else {
            $answers = null;
        }

        return $answers;

    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function insert_id(?string $p_table_name = null, ?string $p_column_name = null): int {
        $sql = version_compare($this->get_version(), '8', '>=') ? 'SELECT SCOPE_IDENTITY() AS last_id' : 'SELECT @@IDENTITY AS last_id';

        $res = $this->execute_query($sql);
        if ($res) {
            $insert_id = $res->row()->last_id ?? 0;
            $res->free_result();

            return $insert_id;
        } else {
            $this->log_error('Can obtain insert id', 'E');

            return 0;
        }
    }


    // --------------------------------------------------------------------

    /**
     *
     * @inheritdoc
     */
    public function is_duplicate_key_error(array $p_error): bool {
        if (isset($p_error['code'])) {
            // for microsoft is a combination of sql_state/error_code
            if ($p_error['code'] == '23000/2627' || $p_error['code'] == '23000/2601') {
                return true;
            }
        }

        return false;
    }

    // --------------------------------------------------------------------

    /**
     *
     * @inheritdoc
     */
    public function is_foreign_key_error(array $p_error): bool {
        if (isset($p_error['code'])) {
            // for microsoft is a combination of sql_state/error_code
            if ($p_error['code'] == '23000/547') {
                return true;
            }
        }

        return false;
    }

    // --------------------------------------------------------------------

    /****************************************************************
     * SQL types stuff
     */
    /**
     *
     * @inheritdoc
     */
    protected function get_normalized_type(string $sql_real_type): string {
        switch (strtolower($sql_real_type)) {
            case 'varchar':
            case 'nvarchar':
            case 'char':
            case 'nchar':
            case 'text':
            case 'ntext':
            case 'date':
            case 'datetime':
            case 'datetime2';
            case 'datetimeoffset';
            case 'smalldatetime':
            case 'time':
            case 'geography':
            case 'bit':
            case 'xml':
                return 'string';

            // otherwise , some kind of numeric
            default:
                return 'numeric';

        }
    }


    /***********************************************************
     * Private helpers
     */


    /**
     * Helper function to get the type of callable , function / sp.
     *
     * @param string $p_callable_name the name of the sp/function
     *
     * @return string|null if the functon doesnt exist return null , otherwise
     * 'p' for procedure , 'f' for functiom.
     *
     * @throws Throwable
     */
    private function _get_callable_type(string $p_callable_name): ?string {
        $answer = null;

        $sql = "SELECT o.name          as procname,
               o.type
        FROM sys.sql_modules AS sm
                 JOIN sys.objects AS o ON sm.object_id = o.object_id
                 JOIN sys.schemas AS ss ON o.schema_id = ss.schema_id
        where o.name = '$p_callable_name'";


        // detect type function or procedure.
        $res = $this->execute_query($sql);

        if ($res) {
            // more than on sp or function with the same name or same name/num parameters is not allowed
            if ($res->num_rows() == 1) {
                $answer = 'p';

                $row = $res->row_array(0);
                if (trim($row['type']) != 'P') {
                    $answer = 'f';
                }

            }
            $res->free_result();
        }

        return $answer;
    }

    // --------------------------------------------------------------------
}