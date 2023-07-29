<?php

return [

    'title' => 'Login',

    'heading' => 'Entre a su cuenta',

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

    'messages' => [

        'failed' => 'Estas credenciales no coinciden con nuestros registros.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Demasiados intentos. Intente de nuevo en :seconds segundos.',
        ],

    ],

];
