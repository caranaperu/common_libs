<?php

/**
 * Procesador default para la lista de campo/valor en JSON.
 * 
 * @author		Carlos Arana Reategui.
 * @license		GPL
 * @since		Version 1.0
 */
class TSLFilterProcessorJson implements TSLIInputProcessor {

    /**
     * Parseara el parametro $processData el cual debera tener la forma siguiente :
     * [{"property":"nombre_campo1","value":"valor_campo_1"},
     *  {"property":"nombre_campo2","value":"valor_campo_2"}]
     *
     * Donde cada elemento del arreglo representa el campo y su valor a
     * aplicar al fitro.
     *
     * @param mixed                 $processData los datos a procesar en el formato indicado.
     * @param TSLRequestConstraints|null $constraints
     *
     * @return array con la lista de campos asociados a su valor answer[x] = "valor"
     *
     */
    public function &process($processData, ?TSLRequestConstraints &$constraints = NULL) {
        $answer = array();
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
                    foreach ($fltconvert as $filterpair) {
                        // Convertimos a un arregloo asociativo de campo y valor
                        $answer[$filterpair['property']] = $filterpair['value'];
                        unset($filterpair);
                    }
                }
                unset($fltconvert);
            }
        }
       // var_dump($answer);
        return $answer;
    }

}

