<?php

return [

    'title' => 'Login',

    'heading' => 'Faça login',

    'actions' => [

        'register' => [
            'before' => 'ou',
            'label' => 'crie uma conta',
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

    'multi_factor' => [

        'heading' => 'Verifique sua identidade',

        'subheading' => 'Para continuar o login, você precisa verificar sua identidade.',

        'form' => [

            'provider' => [
                'label' => 'Como você gostaria de verificar?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Confirmar login',
                ],

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
