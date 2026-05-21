<?php

/**
 * Helper que carga un especifico procesador  de los campos de sort enviados
 * segun los tipos soportados como json,xml,csv.
 * 
 *
 * @author		Carlos Arana Reategui.
 * @license		GPL
 * @since		Version 1.0
 */
class TSLSorterProcessorLoaderHelper implements TSLIProcessorLoaderHelper {

    /**
     * Con los tipos soportados , de no enviarse algun tipo definido en este arreglo se usara
     * json por default.
     * @var string[]
     */
    private static $supported_filters = array('json', 'xml', 'csv');

    /**
     * Carga un procesador de campos de sort para el tipo de formato indicado, de no indicarse ningna
     * usara la implementacion en JSON.
     * 
     * Los tipos de procesadores de sort permitidos son:
     * 'json', 'xml','csv'
     * 
     * Importante : Se asume que si se usan los sorts default , en el include paht default
     * deben estar la referencia a los mismos. (Por ahora estan en config.php).
     *
     * De los 2 parametros usadosa se armara el nombre de la clase del input processor a cargar , tal como
     * "TSLSorterProcessorjson".
     *
     * @param string|null $processor_name , El nombre base del procesador , en el caso no se indique se usara por
     * default "TSLSorterProcessor".
     *
     * @param string|null $format_type , el identificador del tipo de formato tales como  : 'json',
     * 'csv','xml'. De no indicarse se tratara de cargar la generica para json,
     *
     * @param string|null  $library_id No sera usado para este caso.
     *
     * @return TSLIInputProcessor una referencia al Procesador de Sorters o una
     * excepcion de programacion si el tipo de formato no esta soportado soportada
     *
     * @throws TSLProgrammingException en caso de error
     *
     */
    public static function loadProcessor(?string $processor_name = null, string $format_type = null, ?string $library_id = NULL) : TSLIInputProcessor {

        $isUserFilter = true;

        // Si no hay tipo de filtro definido usamos JSON por default
        if (!isset($format_type) || is_null($format_type)) {
            $format_type = 'json';
        }

        // Si no hay nombre base de procesador de filtro usamos los predefinidos
        // de lo contrario se respetara el del usuario.
        if (!isset($processor_name) || is_null($processor_name)) {
            $processor_name = 'TSLSorterProcessor';
            $isUserFilter = false;
        }


        // Si se ha indicado tipo de filtro vemos que este entre los permitidos.
        if (isset($format_type) and in_array($format_type, self::$supported_filters) == FALSE) {
            $backtrace = debug_backtrace();
            throw new TSLProgrammingException($format_type . ' tipo de filtro no soportado , Source= ' . $backtrace[0]['file'] . '-(' . $backtrace[0]['line'] . ')');
        } else {
            // Si es un procesador de usuario lo incluimos , debemos decir
            // que los sorters de usuario deberan estar en el APPPATH dentro del directorio 
            // request/sorters y el nombre del archivo y clase deberan estar compuestos por el nombre
            // base seguido del tipo de Filtro  con la primera letra capitalizada.
            // Ejemplo : MiFiltroJson, MiSuperFiltroCsv , etc.
            if ($isUserFilter) {
                require_once(APPPATH . 'request/sorters/' . $processor_name . ucfirst($format_type) . '.php');
            }
            // Creamos la instancia del filtro.
            $processor_name .= ucfirst($format_type);
            return new $processor_name;
        }
    }

}
