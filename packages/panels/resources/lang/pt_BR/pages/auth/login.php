<?php

return [

    'title' => 'Login',

    'heading' => 'Faça login',

    'actions' => [

        'register' => [
            'before' => 'ou',
            'label' => 'criar uma conta',
        ],

        'request_password_reset' => [
            'label' => 'Esqueceu sua senha?',
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
            'label' => 'Lembre de mim',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Login',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Essas credenciais não correspondem aos nossos registros.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Muitas tentativas de login',
            'body' => 'Por favor tente novamente em :seconds segundos.',
        ],

    ],

];
