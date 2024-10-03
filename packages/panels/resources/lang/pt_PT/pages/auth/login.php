<?php

return [

    'title' => 'Iniciar sessão',

    'heading' => 'Iniciar sessão',

    'actions' => [

        'register' => [
            'before' => 'ou',
            'label' => 'criar uma conta',
        ],

        'request_password_reset' => [
            'label' => 'Esqueceu-se da palavra-passe?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Endereço de e-mail',
        ],

        'password' => [
            'label' => 'Palavra-passe',
        ],

        'remember' => [
            'label' => 'Manter sessão',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Iniciar sessão',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'As credênciais não correspondem aos nossos registos.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Muitas tentativas de início de sessão.',
            'body' => 'Por favor, tente novamente em :seconds segundos.',
        ],

    ],

];
