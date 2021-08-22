<?php

/**
 * Interfase que define los metodos requeridos para un cargador de Constraints,Filters,Sorters, etc
 *
 * @author        Carlos Arana Reategui.
 * @license        GPL
 * @since        Version 1.0
 */
interface TSLIProcessorLoaderHelper {

    /**
     * Carga un procesador de input para el tipo de formato indicado, el cual debera ser especificado
     * al implementarse esta interfase.
     *
     * @param $processor_name|null El nombre base del input processor a cargar, por ejemplo para el caso
     * que el nombre completo de la clase input processor sea "PFilterSmartClientjson" , se debera
     * enviar "PFilter"
     *
     * @param $format_type|null el identificador del tipo de input processor tales como  : 'json',
     * 'csv','xml'. De no indicarse se tratara de cargar la generica para json,
     *
     * @param string|null  $library_id Indica si usa un filtor especifico de libreria soportada
     * , de definirse este parametro este filtor debera existir en la libreria default.
     * Si se ha creado un caso especifico no soportado bastara indicar el primer parametro.
     *
     * @return TSLIInputProcessor una referencia al Input Procesador cargado.
     *
     * @throws TSLProgrammingException en caso de error
     *
     */
    public static function loadProcessor(?string $processor_name = NULL, ?string $format_type = NULL, ?string $library_id = NULL) : TSLIInputProcessor;

}