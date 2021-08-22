<?php

/**
 * Define los metodos a implementar para un procesador de valores de entrada
 * en general (normalmente http parameters), enviados por el cliente.
 *
 * @author		Carlos Arana Reategui.
 * @license		GPL
 * @since		Version 1.0
 */
interface TSLIInputProcessor {

    /**
     * Funcion a implementar para procesar los datos de entrada de acuerdo
     * a los diversos formatos, la cual sera implementado por las clases especificas.
     *
     * @param mixed $processData objeto que representa los datos a procesar , puede
     * ser un string, un array , etc , la interpretacion se hara en los implementadores..
     * @param TSLRequestConstraints|null constraints del request para procesar si fuera necesario.
     *
     * @return mixed Object con los resultados
     */
    public function &process($processData,  ?TSLRequestConstraints &$constraints = NULL);
}

