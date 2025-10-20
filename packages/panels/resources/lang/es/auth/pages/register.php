<?php

return [

    'title' => 'Registrarse',

    'heading' => 'Crear una cuenta',

    'actions' => [

        'login' => [
            'before' => 'o',
            'label' => 'iniciar sesión en su cuenta',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Correo electrónico',
        ],

        'name' => [
            'label' => 'Nombre',
        ],

        'password' => [
            'label' => 'Contraseña',
            'validation_attribute' => 'contraseña',
        ],

        'password_confirmation' => [
            'label' => 'Confirmar contraseña',
        ],

        'actions' => [

            'register' => [
                'label' => 'Registrarse',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Demasiados intentos de registro',
            'body' => 'Por favor, inténtelo de nuevo en :seconds segundos.',
        ],

    ],

];
