<?php

/**
 *
 * Helper que carga un especifico procesador de los campos filtro segun los tipos soportados como json,xml,csv.
 *
 * @author		Carlos Arana Reategui.
 * @license		GPL
 * @since		Version 1.0
 */
class TSLFilterProcessorLoaderHelper implements TSLIProcessorLoaderHelper {

    /**
     * Con los tipos soportados , de no enviarse algun tipo definido en este arreglo se usara
     * json por default.
     * @var string[]
     */
    private static $supported_filters = array('json', 'xml', 'csv');

    /**
     * Carga un procesador de campos de filtro para el tipo de formato indicado, de no indicarse ningna
     * usara la implementacion en JSON.
     *
     * Los formatos de procesadores de constraints permitidos son:
     * 'json', 'xml','csv'
     *
     * Importante : Se asume que si se usan los filtros default , en el include path default
     * deben estar la referencia a los mismos. (Por ahora estan en config.php).
     *
     * De los 3 parametros se armara el nombre de la clase del input processor a cargar , tal como
     * "TSLFilterProcessorSmartClientjson".
     *
     * @param string|null $processor_name , El nombre base del filtro , en el caso no se indique se usara por
     * default "TSLFilterProcessor".
     *
     * @param string|null $format_type , el identificador del tipo de formato tales como  : 'json',
     * 'csv','xml'. De no indicarse se tratara de cargar la generica para json,
     *
     * @param string|null $library_id Indica si usa un filtor especifico de libreria soportada
     * , de definirse este parametro este filtor debera existir en la libreria default.
     * Si se ha creado un caso especifico no soportado bastara indicar el primer parametro.
     *
     * @return TSLIInputProcessor una referencia al Procesador de filtros o una
     * excepcion de programacion si el tipo de formato no esta soportado soportada
     *
     * @throws TSLProgrammingException en caso de error
     *
     */
    public static function loadProcessor(?string $processor_name = NULL, ?string $format_type = NULL, ?string $library_id = NULL) : TSLIInputProcessor {
        //$defaultDBDriver = TSLUtilsHelper::getDefaultDatabaseDriver();

        $isUserFilter = true;

        // Si no hay tipo de filtro definido usamos JSON por default
        if (!isset($filter_id) || is_null($filter_id)) {
            $filter_id = 'json';
        }

        // Si no hay nombre base de procesador de filtro usamos los predefinidos
        // de lo contrario se respetara el del usuario.
        if (!isset($filter_basename) || is_null($filter_basename)) {
            $filter_basename = 'TSLFilterProcessor';
            $isUserFilter = false;
        }

        if ($library_id == NULL) {
            $library_id = '';
        }

        // Si se ha indicado tipo de filtro vemos que este entre los permitidos.
        if (isset($filter_id) and in_array($filter_id, self::$supported_filters) == FALSE) {
            $backtrace = debug_backtrace();
            throw new TSLProgrammingException($filter_id . ' tipo de filtro no soportado , Source= ' . $backtrace[0]['file'] . '-(' . $backtrace[0]['line'] . ')');
        } else {
            // Si es un procesador de usuario lo incluimos , debemos decir
            // que los filtros de usuario deberan estar en el APPPATH dentro del directorio
            // request/sorters y el nombre del archivo y clase deberan estar compuestos por el nombre
            // base seguido del tipo de Filtro  con la primera letra capitalizada.
            // Ejemplo : MiFiltroJson, MiSuperFiltroCsv , etc.
            if ($isUserFilter) {
                require_once(APPPATH . 'request/filters/' . $filter_basename . ucfirst($filter_id) . '.php');
                $filter_basename .= ucfirst($filter_id);
            } else {
                $filter_basename .= ucfirst(strtolower($library_id)).ucfirst($filter_id);
            }
            return new $filter_basename;
        }
    }

}