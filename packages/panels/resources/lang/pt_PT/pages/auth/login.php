<?php

return [

    'title' => 'Login',

    'heading' => 'Iniciar sessão',

    'actions' => [

        'register' => [
            'before' => 'ou',
            'label' => 'criar uma conta',
        ],

        'request_password_reset' => [
            'label' => 'Recuperar password',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-mail',
        ],

        'password' => [
            'label' => 'Senha',
        ],

        'remember' => [
            'label' => 'Manter sessão',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Login',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'As credênciais não correspondem aos nossos registos.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Muitas tentativas de login.',
            'body' => 'Por favor, aguarde :seconds segundos para tentar novamente.',
        ],

    ],

];
