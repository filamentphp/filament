<?php

return [

    'title' => 'Acceso',

    'heading' => 'Entre a su cuenta',

    'actions' => [

        'register' => [
            'before' => 'o',
            'label' => 'Abrir una cuenta',
        ],

        'request_password_reset' => [
            'label' => '¿Ha olvidado su contraseña?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Correo electrónico',
        ],

        'password' => [
            'label' => 'Contraseña',
        ],

        'remember' => [
            'label' => 'Recordarme',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Entrar',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'Verifique su identidad',

        'subheading' => 'Para continuar con el inicio de sesión, deberá verificar su identidad.',

        'form' => [

            'provider' => [
                'label' => '¿Cómo le gustaría verificar?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Confirmar inicio de sesión',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Estas credenciales no coinciden con nuestros registros.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Demasiados intentos. Intente de nuevo en :seconds segundos.',
            'body' => 'Intente de nuevo en :seconds segundos.',
        ],

    ],

];
