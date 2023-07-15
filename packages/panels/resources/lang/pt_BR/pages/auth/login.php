<?php

return [

    'title' => 'Login',

    'heading' => 'Faça login em sua conta',

    'form' => [

        'email' => [
            'label' => 'E-mail',
        ],

        'password' => [
            'label' => 'Senha',
        ],

        'remember' => [
            'label' => 'Lembre de mim',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Login',
            ],

        ],

    ],

    'messages' => [
        'failed' => 'Essas credenciais não correspondem aos com nossos registros.',
        'throttled' => 'Muitas tentativas de login. Por favor, aguarde :seconds segundos para tentar novamente.',
    ],

];
