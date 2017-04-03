<?php

/**
 * Procesador default para constraint de query basado en JSON y especificamente para
 * la libreria smartclient.
 *
 * @author		Carlos Arana Reategui.
 * @license		GPL
 * @since		Version 1.0
 */
class TSLConstraintProcessorSmartclientJson implements TSLIParametersProcessor {

    /**
     * Parseara los parametros para formar los constraints basados en los
     * datos del request.
     *
     * En el caso de SmarClient esos son basicamente :
     * _startRow = primera fila a enviar del total de los resultados.
     * _endRow = Ultima fila a enviar del total de los resultados.
     * @todo  Agregar el resto conforme se vayan implementando.
     *
     *
     * @param mixed $processData para el caso de este processor basado en
     * JSON , el string que contiene el objeto Json con los valores requeridos.
     * @param \TSLRequestConstraints $constraints referencia a un objeto constraints
     * si a existe uno creado.
     *
     * @return mixed en este caso una instancia \TSLRequestConstraints con la estructura de los
     * constraints a procesar.
     */
    public function &process($processData, \TSLRequestConstraints &$constraints = NULL) {
        $startRow = 0;
        $endRow = 0;


        // Si el parametro no es valido retornamos un arreglo en blanco
        if (!isset($processData) || is_null($processData)) {
            return NULL;
        }


        if (isset($processData['_startRow'])) {
            $startRow = $processData['_startRow'];
        }

        if (isset($processData['_endRow'])) {
            $endRow = $processData['_endRow'];
        }

        // Creamos si no se envia uno para usar.
        if (!isset($constraints) || $constraints === NULL) {
            $constraints = new TSLRequestConstraints();
        }

        // Campos de sort (primer intento un solo campo).
        // El sort viene con un negativo adelante si es descendente
        if (isset($processData['_sortBy'])) {
            $pos = strpos($processData['_sortBy'], '-');
            if ($pos !== FALSE) {
                $constraints->addSortField(substr($processData['_sortBy'], 1), 'DESC');
            } else {
                $constraints->addSortField($processData['_sortBy'], 'ASC');
            }
        }

        // Seteamos los datos
        $constraints->setStartRow($startRow);
        $constraints->setEndRow($endRow);

        // Vemos si tenemos que procesar un advanced filter
        // De lo contrario solo efectuamos un filtro normal.
        if (isset($processData['_acriteria'])) {
            $afilter = json_decode($processData['_acriteria']);

            foreach ($afilter->criteria as $elem) {
                $constraints->addFilterField($elem->fieldName, $elem->value, $elem->operator);
            }
        } else {

            // Los campos de filtro , para el smartClient son todos aquellos que
            // no tienen el underscore delante.
            foreach ($processData as $key => $value) {
                // Si empieza con op o libid o parentId son parametros para otors usos no
                // son campos.
                if (!strcmp($key, "op") || !strcmp($key, "libid") || !strcmp($key, "parentId")) {
                    continue;
                }

                // Si no empieza con underscore o _isc (isomorphic smartclient identificador)
                $pos = strpos($key, '_');
                $pos2 = strpos($key, 'isc_');
                if (!($pos === 0 || $pos2 === 0)) {
                    if (isset($processData['_textMatchStyle'])) {
                        $constraints->addFilterField($key, $value, $processData['_textMatchStyle']);
                    } else {
                        $constraints->addFilterField($key, $value, $processData[$key]);
                    }
                }
            }
        }
        return $constraints;
    }

}
