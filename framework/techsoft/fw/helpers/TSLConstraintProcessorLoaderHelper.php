<?php

/**
 * Helper que carga un especifico procesador de los constraints segun los tipos soportados
 * como json,xml,csv.
 *
 *
 * @author		Carlos Arana Reategui.
 * @license		GPL
 * @since		Version 1.0
 */
class TSLConstraintProcessorLoaderHelper implements TSLIProcessorLoaderHelper {

    /**
     * Con los tipos soportados , de no enviarse algun tipo definido en este arreglo se usara
     * json por default.
     * @var string[]
     */
    private static $supported_formats = array('json', 'xml', 'csv');

    /**
     * Carga un procesador de constraints para el tipo de formato indicado, de no indicarse ningna
     * usara la implementacion en JSON.
     *
     * Los formatos de procesadores de constraints permitidos son:
     * 'json', 'xml','csv'
     *
     * Importante : Se asume que si se usan los filtros default , en el include path default
     * deben estar la referencia a los mismos. (Por ahora estan en config.php).
     *
     * De los 3 parametros se armara el nombre de la clase del input processor a cargar , tal como
     * "TSLConstraintProcessorSmartClientjson".
     *
     * @param string|null $processor_name , El nombre base del filtro , en el caso no se indique se usara por
     * default "TSLConstraintProcessor".
     *
     * @param string|null $format_type , el identificador del tipo de formato tales como  : 'json',
     * 'csv','xml'. De no indicarse se tratara de cargar la generica para json,
     *
     * @param string|null $library_id Indica si usa un filtor especifico de libreria soportada
     * , de definirse este parametro este filtor debera existir en la libreria default.
     * Si se ha creado un caso especifico no soportado bastara indicar el primer parametro.
     *
     * @return TSLIInputProcessor una referencia al Procesador de Constraints o una
     * excepcion de programacion si el tipo de formato no esta soportado soportada
     *
     * @throws TSLProgrammingException en caso de error
     *
     */
    public static function loadProcessor(?string $processor_name = NULL, ?string $format_type = NULL, ?string $library_id = NULL) : TSLIInputProcessor {

        $isUserProcessor = true;

        // Si no hay tipo de filtro definido usamos JSON por default
        if (!isset($format_type) || is_null($format_type)) {
            $format_type = 'json';
        }

        // Si no hay nombre base de procesador de filtro usamos los predefinidos
        // de lo contrario se respetara el del usuario.
        if (!isset($processor_name) || is_null($processor_name)) {
            $processor_name = 'TSLConstraintProcessor';
            $isUserProcessor = false;
        }

        if ($library_id == NULL) {
            $library_id = '';
        }

        // Si se ha indicado tipo de filtro vemos que este entre los permitidos.
        if (isset($format_type) and in_array($format_type, self::$supported_formats) == FALSE) {
            $backtrace = debug_backtrace();
            throw new TSLProgrammingException($format_type . ' tipo de filtro no soportado , Source= ' . $backtrace[0]['file'] . '-(' . $backtrace[0]['line'] . ')');
        } else {
            // Si es un procesador de usuario lo incluimos , debemos decir
            // que los filtros de usuario deberan estar en el APPPATH dentro del directorio
            // request/processors y el nombre del archivo y clase deberan estar compuestos por el nombre
            // base seguido del tipo de Filtro  con la primera letra capitalizada.
            // Ejemplo : MiFiltroJson, MiSuperFiltroCsv , etc.
            if ($isUserProcessor) {
                require_once(APPPATH . 'request/processor/' . $processor_name . ucfirst($format_type) . '.php');
                $processor_name .= ucfirst($format_type);
            } else {
                $processor_name .= ucfirst(strtolower($library_id)).ucfirst($format_type);
            }
            return new $processor_name;
        }
    }

}
