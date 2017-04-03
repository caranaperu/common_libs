<?php

/**
 * Define los metodos a implementar para un procesador de parametros
 * en general (normalmente http parameters).
 *
 * @author		Carlos Arana Reategui.
 * @license		GPL
 * @since		Version 1.0
 */
interface TSLIParametersProcessor {

    /**
     * Funcion a implementar para procesar los datos del filtro
     * de acuerdo a los diversos formatos.
     *
     * @param mixed $processData , string, objeto ,etc que representa
     * los datos del filtro.
     * @param \TSLRequestConstraints constraints del request para procesar si fuera necesario.
     *
     * @return mixed Object con los resultados
     */
    public function &process($processData,  \TSLRequestConstraints &$constraints = NULL);
}

