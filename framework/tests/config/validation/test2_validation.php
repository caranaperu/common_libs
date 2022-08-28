<?php
$config = [
    'signup' => [
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
    'email' => [
        [
            'field' => 'emailaddress',
            'label' => 'EmailAddress',
            'rules' => 'required|valid_email'
        ],
        [
            'field' => 'name',
            'label' => 'Name',
            'rules' => 'required|alpha'
        ],
        [
            'field' => 'title',
            'label' => 'Title',
            'rules' => [
                'required',
                ['$this->users_model', 'valid_username']
            ]
        ],
        [
            'field' => 'message',
            'label' => 'MessageBody',
            'rules' => [
                'required',
                ['username_callable', ['$this->users_model', 'valid_username']]
            ]
        ]
    ]
];