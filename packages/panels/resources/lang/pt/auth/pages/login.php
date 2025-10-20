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

    'multi_factor' => [

        'heading' => 'Verificar a sua identidade',

        'subheading' => 'Para continuar a iniciar sessão, tem de verificar a sua identidade.',

        'form' => [

            'provider' => [
                'label' => 'Como pretende verificar?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Confirmar início de sessão',
                ],

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
