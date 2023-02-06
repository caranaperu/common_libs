<?php
$config = [
    'signup_2' => [
        [
            'field' => 'username',
            'label' => 'Username',
            'rules' => 'required'
        ],
        [
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'required'
        ],
        [
            'field' => 'passconf',
            'label' => 'Password Confirmation',
            'rules' => 'required'
        ],
        [
            'field' => 'email',
            'label' => 'Email',
            'rules' => 'required'
        ]
    ],
    'email_2' => [
        [
            'field' => 'emailaddress',
            'label' => 'EmailAddress',
            'rules' => 'required|valid_email'
        ],
        [
            'field' => 'name',
            'label' => 'Name',
            'rules' => 'required|max_length[2]|alpha'
        ],
        [
            'field' => 'title',
            'label' => 'Title',
            'rules' =>    'required',
            'errors' => array(
                'required' => 'You must provide a %s.',
            ),

        ],
        [
            'field' => 'message',
            'label' => 'lang:message',
            'rules' =>    'required'

        ],
        [
            'field' => 'fieldtest2',
            'label' => 'FieldTest2',
            'rules' =>    'callback_fieldtest',
            'errors' => array(
                'fieldtest' => 'You must provide with callback a %s.',
            ),

        ]
    ]
];