<?php

return [

    'title' => 'Restablecer su contraseña',

    'heading' => 'Restablecer su contraseña',

    'buttons' => [

        'reset' => [
            'label' => 'Restablecer contraseña',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'Correo electrónico',
        ],

        'password' => [
            'label' => 'Contraseña',
            'validation_attribute' => 'contraseña',
        ],

        'password_confirmation' => [
            'label' => 'Confirmar contraseña',
        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Demasiados intentos de restablecimiento. Por favor, inténtelo de nuevo en :seconds segundos.',
        ],

    ],

];
