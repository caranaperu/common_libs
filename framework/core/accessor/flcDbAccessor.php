<?php

namespace framework\core\accessor;


use framework\core\model\flcBaseModel;
use framework\database\driver\flcDriver;
use InvalidArgumentException;
use Throwable;


abstract class flcDbAccessor extends flcPersistenceAccessor {

    static array $db_error_codes = [
        // Not database related
        'DB_NO_FIELDS_TO_EXECUTE' => 100,

        // dataBASE RELATED
        'DB_OPERATION_OK' => 100000,
        'DB_NOT_UNIQUE_ID' => 100001,
        'DB_RECORD_NOT_EXIST' => 100002,
        'DB_RECORD_MODIFIED' => 100003,
        'DB_FAIL_OPEN' => 100004,
        'DB_OPERATION_FAIL' => 100100
    ];


    protected flcDriver    $db;
    protected flcBaseModel $model;
    /**
     * @var resource
     */
    protected $conn_id;

    public function __construct(?flcDriver $p_db = null) {
        $this->db = $p_db;
        // Because nested transaction are completed different implemented and also many times not really
        // supported by databases , we work under unique transaction.
        $this->db->set_trans_unique(true);
        $this->db->open();


    }


    public function add(array $p_id, array $p_fields): bool {
        // TODO: Implement add() method.
        return false;
    }

    /**
     * RECORDAR que p_open_close forzar open solo debe hacerse en la primera operacion bajo la coneccion
     * forzar close debe ser hecho sobre la ultima operacion.
     *
     * @param flcBaseModel $p_model
     * @param string|null  $p_suboperation
     * @param bool         $p_open_close
     *
     * @return int
     */
    public function update(flcBaseModel &$p_model, ?string $p_suboperation = null, int $p_open_close = 0): int {

        $ret = self::$db_error_codes['DB_OPERATION_OK'];
        $tran_open = $this->db->is_trans_open();
        $res = null;

        try {
            if ($p_open_close & self::$open_close_flags['DB_OPEN_FLAG']) {
                // open only works one time if its already opened do nothig.
                if (!$this->db->open()) {
                    $this->db->log_error('Cant open database', 'E');

                    return self::$db_error_codes['DB_FAIL_OPEN'];
                }
            }

            $rowversion_support = $this->is_rowversion_supported($p_model);

            // transaction support

            if (!$tran_open) {
                $this->db->trans_begin();
            }

            $sql = $this->_get_update_query($p_model, $p_suboperation);

            $res = $this->db->execute_query($sql);

            if ($res) {
                echo 'affected rows '.$res->affected_rows().PHP_EOL;
                $affected_rows = $res->affected_rows();


                if ($affected_rows <= 0) {
                    // Probably nothing in the record is change or a real error ocurred
                    // check.
                    $error = $this->db->error();

                    if (!$this->is_db_error($error)) {
                        // if simply nothing was changed , then no error , next step
                        // reread to verify changes.
                        if ($rowversion_support) {
                            echo 'ejecutando verificacion de record modified'.PHP_EOL;

                            // its deleted or modified?
                            // its delete will return self::$db_error_codes['DB_RECORD_NOT_EXIST']
                            $ret = $this->read($p_model, $p_suboperation, false);
                            if ($ret == self::$db_error_codes['DB_OPERATION_OK']) {
                                $ret = self::$db_error_codes['DB_RECORD_MODIFIED'];
                            }

                        }
                    } else {
                        $this->db->log_error("Error : {$error['code']} - {$error['message']}", 'E');
                        $ret = self::$db_error_codes['DB_OPERATION_FAIL'];
                    }

                } else {
                    $ret = $this->read($p_model, $p_suboperation, false);
                }


            } else {
                $error = $this->db->error();
                $this->db->log_error("Cant execute update", 'E');
                $ret = self::$db_error_codes['DB_OPERATION_FAIL'];

            }
        } catch (Throwable $ex) {
            // si es exception posiblemente no es error de database
            $error = $this->db->error();
            $this->db->log_error("Cant execute update", 'E');
            $ret = self::$db_error_codes['DB_OPERATION_FAIL'];

        } finally {
            // close transaction if required
            if (!$tran_open) {
                $this->db->trans_complete();
            }
            if ($res) {
                $res->free_result();
            }

            // if this operation can  close
            if ($p_open_close & self::$open_close_flags['DB_CLOSE_FLAG']) {
                if ($this->db->is_open()) {
                    $this->db->close();
                }
            }
        }


        return $ret;
    }

    public function delete(array $p_fields, array $p_constraints, string $p_type): bool {
        return false;
    }

    public function read(flcBaseModel &$p_model, ?string $p_suboperation = null, int $p_open_close = 0): int {

        $ret = self::$db_error_codes['DB_OPERATION_OK'];
        try {
            if ($p_open_close & self::$open_close_flags['DB_OPEN_FLAG']) {
                // open only works one time if its already opened do nothig.
                if (!$this->db->open()) {
                    $this->db->log_error('Cant open database', 'E');

                    return self::$db_error_codes['DB_FAIL_OPEN'];
                }
            }

            $res = null;

            $sql = $this->_get_read_query($p_model, $p_suboperation);

            $res = $this->db->execute_query($sql);
            echo 'affected rows read '.$res->affected_rows().PHP_EOL;

            if ($res) {
                if ($res->num_rows() == 0) {
                    $ret = self::$db_error_codes['DB_RECORD_NOT_EXIST'];
                } else {
                    $p_model->set_values($res->row_array());
                }

                //$res->free_result();
            } else {
                // throw cant execute
                print_r($this->db->error());

                $ret = self::$db_error_codes['DB_OPERATION_FAIL'];
            }
        } catch (Throwable $ex) {
            print_r($ex);
        } finally {
            if ($res) {
                $res->free_result();
            }

            // if this operation can  close
            if ($p_open_close & self::$open_close_flags['DB_CLOSE_FLAG']) {
                if ($this->db->is_open()) {
                    $this->db->close();
                }
            }

        }

        return $ret;
    }

    public function fetch(array $p_fields, array $p_constraints, string $p_suboperation): bool {
        return false;
    }

    protected abstract function _get_add_query(): string;

    protected function _get_update_query(flcBaseModel &$p_model, ?string $p_suboperation = null): string {
        $fields = $p_model->get_fields();

        if (count($fields) == 0) {
            throw new InvalidArgumentException('Not defined fields to read', self::$db_error_codes['DB_NO_FIELDS_TO_EXECUTE']);
        }

        // verify ids are defines
        $ids = $p_model->get_ids();
        if (!isset($ids) || count($ids) == 0) {
            throw new InvalidArgumentException('Not unique id fiels are defined for update a record', self::$db_error_codes['DB_NOT_UNIQUE_ID']);

        }
        foreach ($ids as $id) {
            if (!array_key_exists($id, $fields) || empty($fields[$id])) {
                throw new InvalidArgumentException('Not all fields needed to identify unique a record are defined for update a record', self::$db_error_codes['DB_NOT_UNIQUE_ID']);
            }
        }

        // update list
        $sql = 'update '.$p_model->get_table_name().' set ';
        foreach ($fields as $field => $value) {
            $type = $p_model->get_field_type($field) ?? '';
            $value = $value ?? '';

            $sql .= $field.'='.$this->get_normalized_field_value($field, $value, $type).',';
        }

        // where list
        $sql = rtrim($sql, ',').' where ';
        foreach ($ids as $id) {
            $type = $p_model->get_field_type($id) ?? '';
            $value = $fields[$id] ?? '';

            $sql .= $id.'='.$this->get_normalized_field_value($id, $value, $type).' and ';
        }

        // add rowversion field to where
        // check if row version is supported
        if ($this->is_rowversion_supported($p_model)) {
            $value = $p_model->{$this->db->get_rowversion_field()} ?? '';
            $type = $p_model->get_field_type($this->db->get_rowversion_field()) ?? '';

            $sql .= $this->db->get_rowversion_field().' = '.$this->get_normalized_field_value($this->db->get_rowversion_field(), $value, $type);
        } else {
            $sql = rtrim($sql, ' and ');
        }

        echo $sql.PHP_EOL;

        return $sql;

    }

    protected abstract function _get_delete_query(): string;

    protected function _get_read_query(flcBaseModel &$p_model, ?string $p_suboperation = null): string {

        $fields = $p_model->get_fields();

        if (count($fields) == 0) {
            throw new InvalidArgumentException('Not defined fields to read', self::$db_error_codes['DB_NO_FIELDS_TO_EXECUTE']);
        }


        // verify ids are defines
        $ids = $p_model->get_ids();
        if (!isset($ids) || count($ids) == 0) {
            throw new InvalidArgumentException('Not unique id fiels are defined for read a record', self::$db_error_codes['DB_NOT_UNIQUE_ID']);

        }
        foreach ($ids as $id) {
            if (!array_key_exists($id, $fields) || empty($fields[$id])) {
                throw new InvalidArgumentException('Not all fields needed to identify unique a record are defined for read a record', self::$db_error_codes['DB_NOT_UNIQUE_ID']);
            }
        }


        // create the
        $sql = 'select ';
        foreach ($fields as $field => $value) {
            $sql .= $field.',';
        }
        $sql = rtrim($sql, ',');
        $sql .= ' from '.$p_model->get_table_name().' where ';

        foreach ($ids as $id) {
            $sql .= $id.'='.$fields[$id].' and ';
        }

        $sql = rtrim($sql, ' and ');

        echo $sql.PHP_EOL;

        return $sql;
    }

    protected abstract function _get_fetch_query(): string;

    /******************************************************************************
     * Utilities
     */


    protected function is_db_error(array $error): bool {
        // only message , postgres for example no return error code only message
        if (!empty($error->message)) {
            return true;
        }

        return false;
    }

    protected function is_rowversion_supported(flcBaseModel $p_model): bool {
        $rowversion_field = $this->db->get_rowversion_field();
        if (!empty($rowversion_field)) {
            if (array_key_exists($rowversion_field, $p_model->get_fields())) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $p_fieldname
     * @param mixed  $p_value
     * @param string $p_type
     *
     * @return mixed
     */
    protected function get_normalized_field_value(string $p_fieldname, $p_value, string $p_type) {
        if ($p_type == 'bool') {
            return ($p_value ? '1' : '0');
        } else {
            if ($p_type == 'nostring' || !is_string($p_value)) {
                return $p_value;
            } else {
                return "'$p_value'";
            }
        }
    }


}