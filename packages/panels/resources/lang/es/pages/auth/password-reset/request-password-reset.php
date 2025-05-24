<?php

return [

    'title' => 'Restablecer tu contraseña',

    'heading' => '¿Olvidaste tu contraseña?',

    'actions' => [

        'login' => [
            'label' => 'Volver al inicio de sesión',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Correo electrónico',
        ],

        'actions' => [

            'request' => [
                'label' => 'Enviar email',
            ],

        ],

    ],

    'notifications' => [

        'sent' => [
            'body' => 'Si su cuenta no existe, no recibirá el correo electrónico.',
        ],

        'throttled' => [
            'title' => 'Demasiadas solicitudes',
            'body' => 'Por favor, inténtelo de nuevo en :seconds segundos.',
        ],

    ],

];
