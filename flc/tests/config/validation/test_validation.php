<?php

$config['v_entidad_registro_propiedad'] = array(
    'getEntidadRegistroPropiedad' => array(
        array(
            'field' => 'entidad_registro_propiedad_codigo',
            'label' => 'lang:entidad_registro_propiedad_codigo',
            'rules' => 'required|alpha|max_length[15]'
        )
    ),
    'updEntidadRegistroPropiedad' => array(
        array(
            'field' => 'entidad_registro_propiedad_codigo',
            'label' => 'lang:entidad_registro_propiedad_codigo',
            'rules' => 'required|alpha|max_length[15]'
        ),
        array(
            'field' => 'entidad_registro_propiedad_descripcion',
            'label' => 'lang:entidad_registro_propiedad_descripcion',
            'rules' => 'required|max_length[120]'
        ),
        array(
            'field' => 'paises_codigo',
            'label' => 'lang:paises_codigo',
            'rules' => 'required|max_length[15]'
        ),
        array(
            'field' => 'moneda_codigo',
            'label' => 'lang:paises_codigo',
            'rules' => 'required|alpha|max_length[8]'
        ),
        array(
            'field' => 'versionId',
            'label' => 'lang:versionId',
            'rules' => 'required|integer'
        )
    ),
    'delEntidadRegistroPropiedad' => array(
        array(
            'field' => 'entidad_registro_propiedad_codigo',
            'label' => 'lang:entidad_registro_propiedad_codigo',
            'rules' => 'required|alpha|max_length[15]'
        ),
        array(
            'field' => 'versionId',
            'label' => 'lang:versionId',
            'rules' => 'required|integer'
        )
    ),
    'addEntidadRegistroPropiedad' => array(
        array(
            'field' => 'entidad_registro_propiedad_codigo',
            'label' => 'lang:entidad_registro_propiedad_codigo',
            'rules' => 'required|alpha|max_length[15]'
        ),
        array(
            'field' => 'entidad_registro_propiedad_descripcion',
            'label' => 'lang:entidad_registro_propiedad_descripcion',
            'rules' => 'required|max_length[120]'
        ),
        array(
            'field' => 'paises_codigo',
            'label' => 'lang:paises_codigo',
            'rules' => 'required|max_length[15]'
        ),
        array(
            'field' => 'moneda_codigo',
            'label' => 'lang:paises_codigo',
            'rules' => 'required|alpha|max_length[8]'
        )
    )
);