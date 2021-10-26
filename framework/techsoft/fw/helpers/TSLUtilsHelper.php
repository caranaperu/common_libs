<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Clase  helper para funciones requeridas por diversas partes del framework.
 *
 * @author		Carlos Arana Reategui.
 * @license		GPL
 * @since		Version 1.0
 */
class TSLUtilsHelper {

    /**
     * Busca en la definicion de la base de datos (database.php)
     * si se ha definido una default , de ser asi retorna su
     * identificador.
     * Por Ejemplo :
     * <pre>
     * $active_group = 'default';
     * $query_builder = TRUE;
     *
     * $db['default'] = array(
     * 'dsn'	=> '',
     * 'hostname' => 'localhost',
     * 'username' => 'postgres',
     * 'password' => 'melivane',
     * 'database' => 'db_laboweb',
     * 'dbdriver' => 'postgre',
     * 'dbprefix' => '',
     * 'pconnect' => FALSE,
     * 'db_debug' => FALSE,
     * 'cache_on' => FALSE,
     * 'cachedir' => '',
     * 'char_set' => 'utf8',
     * 'dbcollat' => 'utf8_general_ci',
     * 'swap_pre' => '',
     * 'encrypt' => FALSE,
     * 'compress' => FALSE,
     * 'stricton' => FALSE,
     * 'failover' => array(),
     * 'save_queries' => FALSE
     * );
     *</pre>
     *
     * En este caso el vamor de la variable $active_group.
     *
     * @return string|null el driver default o null si no hay definicion.
     */
    public static function getDefaultDatabase() : ?string {
        if (!isset(self::$defaultDb)) {
            require (APPPATH . '/config/database.php');
            // Si se ha definido el active_group
            // o base de datos default y asi mismo se ha definido que driver usas la misma , se
            // retorna su valor , de lo contrario null
            if (isset($active_group) and empty($active_group) == FALSE and
                isset($db[$active_group]['dbdriver'])) {
                self::$defaultDb = $active_group;
            }
        }
        return self::$defaultDb;
    }
    private static $defaultDbDriver = NULL;

    private static $defaultDb = NULL;

    /**
     * Busca en la definicion de la base de datos (database.php)
     * si se ha definido una default , de ser asi retorna el driver
     * que la maneja. Este por ejemplo puede ser postgre,mysql, etc.
     * Por Ejemplo :
     * <pre>
     * $active_group = 'default';
     * $query_builder = TRUE;
     *
     * $db['default'] = array(
     * 'dsn'	=> '',
     * 'hostname' => 'localhost',
     * 'username' => 'postgres',
     * 'password' => 'melivane',
     * 'database' => 'db_laboweb',
     * 'dbdriver' => 'postgre',
     * 'dbprefix' => '',
     * 'pconnect' => FALSE,
     * 'db_debug' => FALSE,
     * 'cache_on' => FALSE,
     * 'cachedir' => '',
     * 'char_set' => 'utf8',
     * 'dbcollat' => 'utf8_general_ci',
     * 'swap_pre' => '',
     * 'encrypt' => FALSE,
     * 'compress' => FALSE,
     * 'stricton' => FALSE,
     * 'failover' => array(),
     * 'save_queries' => FALSE
     * );
     *</pre>
     *
     * En este caso buscara el campo dbdriver.
     *
     * @return string|null el driver default o null si no hay definicion.
     */
    public static function getDefaultDatabaseDriver() : ?string {
        if (!isset(self::$defaultDbDriver)) {
            require (APPPATH . '/config/database.php');
            // Si se ha definido el active_group
            // o base de datos default y asi mismo se ha definido que driver usas la misma , se
            // retorna su valor , de lo contrario null
            if (isset($active_group) and empty($active_group) == FALSE and
                    isset($db[$active_group]['dbdriver'])) {
                self::$defaultDbDriver = $db[$active_group]['dbdriver'];
            }
        }
        return self::$defaultDbDriver;
    }

    /**
     * Codifica el valor del parametro de entrada a UTF-8, lo hara recursivamente de
     * ser necesario.
     *
     * @param mixed $itm con el input a procesar.
     *
     * @return array|string retorna el valor del parametro encoded a UTF-8
     */
    public static function array_ut8_encode_recursive(&$itm) {
        $new = $itm;
        if (is_string($new))
            return utf8_encode($new);
        if (is_array($new))
            return array_map('TSLUtilsHelper::array_ut8_encode_recursive', $new);
        if (is_object($new))
            foreach (get_object_vars($new) as $key => $value)
                $new->$key = TSLUtilsHelper::array_ut8_encode_recursive($new->$key);
        return $new;
    }

    /**
     * Esta funcion sanitiza la salida el string enviado.
     *
     * @param string $url la URL a sanitizar
     * @return string con El url sanitizado.
     */
    public static function esc_url(string $url) : string {

        if ('' == $url) {
            return $url;
        }

        $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);

        $strip = array('%0d', '%0a', '%0D', '%0A');
        $url = (string) $url;

        $count = 1;
        while ($count) {
            $url = str_replace($strip, '', $url, $count);
        }

        $url = str_replace(';//', '://', $url);

        $url = htmlentities($url);

        $url = str_replace('&amp;', '&#038;', $url);
        $url = str_replace("'", '&#039;', $url);

        if ($url[0] !== '/') {
            // We're only interested in relative links from $_SERVER['PHP_SELF']
            return '';
        } else {
            return $url;
        }
    }

    /**
     * Metodo que eliminara uno o mas archivos dependiendo , si no se indica directoria en el
     * $FilePath o como parte del $filePattern se borrara los archivos en el directorio actual
     * de lo contrario en el indicado.
     *
     * Tratara de eliminar todos los que pueda asi alguno de error al eliminar.
     *
     * @param string      $filePattern por ejemplo "*.jpg" o "/var/*.txt"
     *
     * @return bool true si todos los archivos fueron borrados , false si al menos uno no pudo
     * ser borrado , como seria el caso que este abierto por otro proceso.
     */
    public static function removeDirFiles(string $filePattern) : bool {
        if (isset($filePattern)) {

            $ret = true;
            // Eliminara tosdos los archivos que pueda aunque encuentre error eliminado alguno , por ejemplo
            // por estar usado o abierto,.
            foreach (glob($filePattern) as $filename) {
                if (unlink($filename) == false) {
                    $ret = false;
                }
            }
        }
        return $ret;
    }

}
