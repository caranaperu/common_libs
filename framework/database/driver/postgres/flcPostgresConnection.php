<?php

namespace framework\database\driver\postgres;

use framework\database\flcConnection;


class flcPostgresConnection extends flcConnection {

    /**
     * For postgres the dsn prototype will be : 'driver://username:password@hostname/database'
     *
     * @inheritDoc
     */
    public function initialize(?string $dsn, ?string $host, ?int $port, ?string $database, ?string $user, ?string $password, string $charset = 'utf8', string $collation = 'utf8_general_ci'): bool {
        // Extract dsn parts if well defined, if the values are on dsn they are taken otherwise extract
        // them from the parameters

        $query = "";
        if (($parsedDsn = @parse_url($dsn)) !== false) {
            $host = (isset($parsedDsn['host']) ? rawurldecode($parsedDsn['host']) : $host);
            $port = (isset($parsedDsn['port']) ? rawurldecode($parsedDsn['port']) : $port);
            $user = (isset($parsedDsn['user']) ? rawurldecode($parsedDsn['user']) : $user);
            $password = (isset($parsedDsn['pass']) ? rawurldecode($parsedDsn['pass']) : $password);
            $database = (isset($parsedDsn['database']) ? rawurldecode($parsedDsn['database']) : $database);
            $query = isset($parsedDsn['query']) ? rawurldecode($parsedDsn['query']) : "";

        }

        // generate dsn if its possible.

        // Set default if values not defined , generate the full dsn for postgres
        if (!isset($port)) {
            $port = '5432';
        }

        // Check values
        if (!isset($host) || !isset($user) || !isset($password) || !isset($database)) {
            return false;
        } else {
            $this->dsn = 'postgresql://'.$user.':'.$password.'@'.$host.':'.$port.'/'.$database;
            if ($query && $query != "") {
                $this->dsn .= '&'.$query;
            }
        }

        // preserve the collation
        if ($collation) {
            $this->collation = $collation;
        }

        // preserve the charset
        if ($charset) {
            $this->charset = $charset;
        }

        return true;
    }

    /**
     * Set client character set
     *
     * @param string $charset see postgresql docs.
     *
     * @return    bool false if cant set the character encoding
     */
    protected function _set_charset(string $charset): bool {
        // Check if open is called before
        if ($this->connId) {
            return (pg_set_client_encoding($this->connId, $charset) === 0);
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    protected function _open()  {
        return pg_connect($this->dsn);
    }

    protected function _close(): void {
        pg_close($this->connId);
    }


}