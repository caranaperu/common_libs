<?php

/**
 * Procesador default para generar los constraints a la persistencia basado en un input en JSON y
 * especificamente para la libreria smartclient.
 * Obviamente si se genera el JSON adecuado desde el cliente podria usarse en cualquier libreria.
 *
 * @author		Carlos Arana Reategui.
 * @license		GPL
 * @since		Version 1.0
 */
class TSLConstraintProcessorSmartclientJson implements TSLIInputProcessor {

    /**
     * Parseara los parametros para formar los constraints basados en los
     * datos del request.
     *
     * En el caso de SmarClient esos son basicamente : <br>
     * _startRow = primera fila a enviar del total de los resultados. <br>
     * _endRow = Ultima fila a enviar del total de los resultados. <br>
     * _sortBy = campo de sort <br>
     * _acriteria = lista de campos en JSON con cada elemento indicando el nombre
     * de campo,valor y operador para el campo/valor , mayor que , igual , etc. <br>
     *
     * En el caso que _acriteria no este definido se recibira una lista de campos/valor
     * a usarse como filtro a la persitencia , en el caso de un DB serian los campos
     * a usarse en el where. Adicionalmente en este caso podria venir definido : <br>
     * _textMatchStyle con el valor 'exact' de no estar presente la busqueda sera por ejemplo
     * con like.
     *
     * @param mixed $processData para el caso de este processor basado en
     * JSON , el string que contiene el objeto Json con los valores requeridos.
     * @param TSLRequestConstraints|null $constraints referencia a un objeto constraints
     * si a null para lo cual sera creado en ese metodo.
     *
     * @return mixed en este caso una instancia \TSLRequestConstraints con la estructura de los
     * constraints a procesar.
     */
    public function &process($processData, ?TSLRequestConstraints &$constraints = NULL) {
        $startRow = 0;
        $endRow = 0;


        // Si el parametro no es valido retornamos un arreglo en blanco
        if (!isset($processData) || is_null($processData)) {
            return null;
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
