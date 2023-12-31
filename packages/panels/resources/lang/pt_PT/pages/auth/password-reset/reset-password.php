<?php

return [

    'title' => 'Redefinir a sua palavra-passe',

    'heading' => 'Redefinir a sua palavra-passe',

    'form' => [

        'email' => [
            'label' => 'Endereço de e-mail',
        ],

        'password' => [
            'label' => 'Palavra-passe',
            'validation_attribute' => 'palavra-passe',
        ],

        'password_confirmation' => [
            'label' => 'Confirmar palavra-passe',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Redefinir palavra-passe',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Muitas tentativas de redefinição',
            'body' => 'Por favor, tente novamente em :seconds segundos.',
        ],

    ],

];
