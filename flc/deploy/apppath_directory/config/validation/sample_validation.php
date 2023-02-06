<?php

$config['main_group'] = array(
// Para la lectura de un contribuyente basado eb su id
    'subgroup_1' => array(
        array(
            'field' => 'field_name',
            'label' => 'lang:field_name',
            'rules' => 'required|alpha_numeric|max_length[15]'
        )
    ),
    'subgroup2' => array(
        array(
            'field' => 'field_name_1',
            'label' => 'lang:field_name_1',
            'rules' => 'required|alpha_numeric|max_length[15]'
        ),
        array(
            'field' => 'field_name_1',
            'label' => 'lang:field_name_2',
            'rules' => 'required|max_length[60]'
        )
        // etc.....
    )
);