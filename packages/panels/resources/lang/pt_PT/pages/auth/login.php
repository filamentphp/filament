<?php

return [

    'title' => 'Login',

    'heading' => 'Iniciar sessão',

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
            'title' => 'Muitas tentativas de login. Por favor, aguarde :seconds segundos para tentar novamente.',
        ],

    ],

];
