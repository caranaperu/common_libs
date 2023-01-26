<?php

$config['v_atletas'] = array(
// Para la lectura de un contribuyente basado eb su id
    'getAtletas' => array(
        array(
            'field' => 'atletas_codigo',
            'label' => 'lang:atletas_codigo',
            'rules' => 'required|alpha_numeric|max_length[15]'
        )
    ),
    'updAtletas' => array(
        array(
            'field' => 'atletas_codigo',
            'label' => 'lang:atletas_codigo',
            'rules' => 'required|alpha_numeric|max_length[15]'
        ),
        array(
            'field' => 'atletas_ap_paterno',
            'label' => 'lang:atletas_ap_paterno',
            'rules' => 'required|max_length[60]'
        ),
        array(
            'field' => 'atletas_ap_materno',
            'label' => 'lang:atletas_ap_materno',
            'rules' => 'max_length[5]'
        ),
        array(
            'field' => 'atletas_nombres',
            'label' => 'lang:atletas_nombres',
            'rules' => 'required|max_length[120]'
        ),
        array(
            'field' => 'paises_codigo',
            'label' => 'lang:paises_codigo',
            'rules' => 'required|max_length[15]'
        ),
        array(
            'field' => 'atletas_sexo',
            'label' => 'lang:atletas_sexo',
            'rules' => 'required|alpha|max_length[1]'
        ),
        array(
            'field' => 'atletas_nro_documento',
            'label' => 'lang:atletas_nro_documento',
            'rules' => 'integer|max_length[8]'
        ),
        array(
            'field' => 'atletas_nro_pasaporte',
            'label' => 'lang:atletas_nro_pasaporte',
            'rules' => 'integer|min_length[7]|max_length[8]'
        ),
        array(
            'field' => 'atletas_fecha_nacimiento',
            'label' => 'lang:atletas_fecha_nacimiento',
            'rules' => 'required|valid_date'
        ),
        array(
            'field' => 'atletas_telefono_casa',
            'label' => 'lang:atletas_telefono_casa',
            'rules' => 'is_natural|min_length[7]|max_length[10]'
        ),
        array(
            'field' => 'atletas_telefono_celular',
            'label' => 'lang:atletas_telefono_celular',
            'rules' => 'integer|min_length[9]|max_length[13]'
        ),
        array(
            'field' => 'atletas_email',
            'label' => 'lang:atletas_email',
            'rules' => 'valid_email|max_length[150]'
        ),
        array(
            'field' => 'atletas_direccion',
            'label' => 'lang:atletas_direccion',
            'rules' => 'required|max_length[250]'
        ),
        array(
            'field' => 'atletas_observaciones',
            'label' => 'lang:atletas_observaciones',
            'rules' => 'max_length[250]'
        ),
        array(
            'field' => 'atletas_talla_ropa_buzo',
            'label' => 'lang:atletas_talla_ropa_buzo',
            'rules' => 'required|max_length[3]'
        ),
        array(
            'field' => 'atletas_talla_ropa_poloshort',
            'label' => 'lang:atletas_talla_ropa_poloshort',
            'rules' => 'required|max_length[3]'
        ),
        array(
            'field' => 'atletas_talla_zapatillas',
            'label' => 'lang:atletas_talla_zapatillas',
            'rules' => 'decimal|max_length[4]'
        ),
        array(
            'field' => 'atletas_norma_zapatillas',
            'label' => 'lang:atletas_norma_zapatillas',
            'rules' => 'required|max_length[2]'
        ),
        array(
            'field' => 'atletas_url_foto',
            'label' => 'lang:atletas_url_foto',
            'rules' => 'max_length[300]'
        ),
        array(
            'field' => 'versionId',
            'label' => 'lang:versionId',
            'rules' => 'required|integer'
        )
    ),
    'delAtletas' => array(
        array(
            'field' => 'atletas_codigo',
            'label' => 'lang:atletas_codigo',
            'rules' => 'required|alpha_numeric|max_length[15]'
        ),
        array(
            'field' => 'versionId',
            'label' => 'lang:versionId',
            'rules' => 'required|integer'
        )
    ),
    'addAtletas' => array(
        array(
            'field' => 'atletas_codigo',
            'label' => 'lang:atletas_codigo',
            'rules' => 'required|alpha_numeric|max_length[15]'
        ),
        array(
            'field' => 'atletas_ap_paterno',
            'label' => 'lang:atletas_ap_paterno',
            'rules' => 'required|max_length[60]'
        ),
        array(
            'field' => 'atletas_ap_materno',
            'label' => 'lang:atletas_ap_materno',
            'rules' => 'max_length[60]'
        ),
        array(
            'field' => 'atletas_nombres',
            'label' => 'lang:atletas_nombres',
            'rules' => 'required|max_length[120]'
        ),
        array(
            'field' => 'paises_codigo',
            'label' => 'lang:paises_codigo',
            'rules' => 'required|max_length[15]'
        ),
        array(
            'field' => 'atletas_sexo',
            'label' => 'lang:atletas_sexo',
            'rules' => 'required|alpha|max_length[1]'
        ),
        array(
            'field' => 'atletas_nro_documento',
            'label' => 'lang:atletas_nro_documento',
            'rules' => 'integer|max_length[8]'
        ),
        array(
            'field' => 'atletas_nro_pasaporte',
            'label' => 'lang:atletas_nro_pasaporte',
            'rules' => 'integer|min_length[7]|max_length[8]'
        ),
        array(
            'field' => 'atletas_fecha_nacimiento',
            'label' => 'lang:atletas_fecha_nacimiento',
            'rules' => 'required|valid_date'
        ),
        array(
            'field' => 'atletas_telefono_casa',
            'label' => 'lang:atletas_telefono_casa',
            'rules' => 'is_natural|min_length[7]|max_length[10]'
        ),
        array(
            'field' => 'atletas_telefono_celular',
            'label' => 'lang:atletas_telefono_celular',
            'rules' => 'integer|min_length[9]|max_length[13]'
        ),
        array(
            'field' => 'atletas_email',
            'label' => 'lang:atletas_email',
            'rules' => 'valid_email|max_length[150]'
        ),
        array(
            'field' => 'atletas_direccion',
            'label' => 'lang:atletas_direccion',
            'rules' => 'required|max_length[250]'
        ),
        array(
            'field' => 'atletas_observaciones',
            'label' => 'lang:atletas_observaciones',
            'rules' => 'max_length[250]'
        ),
        array(
            'field' => 'atletas_talla_ropa_buzo',
            'label' => 'lang:atletas_talla_ropa_buzo',
            'rules' => 'required|max_length[3]'
        ),
        array(
            'field' => 'atletas_talla_ropa_poloshort',
            'label' => 'lang:atletas_talla_ropa_poloshort',
            'rules' => 'required|max_length[3]'
        ),
        array(
            'field' => 'atletas_talla_zapatillas',
            'label' => 'lang:atletas_talla_zapatillas',
            'rules' => 'decimal|max_length[4]'
        ),
        array(
            'field' => 'atletas_norma_zapatillas',
            'label' => 'lang:atletas_norma_zapatillas',
            'rules' => 'required|max_length[2]'
        ),
        array(
            'field' => 'atletas_url_foto',
            'label' => 'lang:atletas_url_foto',
            'rules' => 'max_length[300]'
        )
    )
);
?>