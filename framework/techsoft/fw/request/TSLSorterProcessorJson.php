<?php

/**
 * Procesador default para la lista de nombre campo/valor/direccion de sort en JSON.
 *
 * @author        Carlos Arana Reategui.
 * @license        GPL
 * @since        Version 1.0
 */
class TSLSorterProcessorJson implements TSLIInputProcessor {

    /**
     * Parseara el parametro el cual debera tener la forma siguiente :
     * [{"property":"nombre_campo1","value":"ASC | DESC"},
     *  {"property":"nombre_campo2","value":"ASC | DESC"}]
     *
     * Donde cada elemento del arreglo representa el campo y el tipo
     * de filto a aplicar.
     *
     * @param mixed                      $processData un texto que representa un arreglo de elementos
     * JSON de acuerdo a la documentacion.
     *
     * @param TSLRequestConstraints|null $constraints
     *
     * @return array|Object
     */
    public function &process($processData, ?TSLRequestConstraints &$constraints = NULL) {
        $answer = [];
        // Si el parametro no es valido retornamos un arreglo en blanco
        if (!isset($processData) || is_null($processData)) {
            return $answer;
        }

        // Decodificamos
        $fltconvert = json_decode($processData, true);

        //var_dump(json_last_error());
        //
        // Si no ha habido errores continuamos
        if (json_last_error() == JSON_ERROR_NONE) {
            // Si hay respuestas contimuamos
            if (isset($fltconvert) and is_array($fltconvert)) {
                if (count($fltconvert) > 0) {
                    // Estraemos los resultados y los pasamos a una forma simplificada
                    // de arreglo asociativo
                    foreach ($fltconvert as $sortpair) {
                        // Convertimos a un arregloo asociativo de campo y valor
                        $answer[$sortpair['property']] = $sortpair['direction'];
                        unset($sortpair);
                    }
                }
                unset($fltconvert);
            }
        }

        // var_dump($answer);
        return $answer;
    }

}

