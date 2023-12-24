<?php

return [

    'title' => 'Registar',

    'heading' => 'Registe-se',

    'actions' => [

        'login' => [
            'before' => 'ou',
            'label' => 'iniciar sessão na sua conta',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Endereço de e-mail',
        ],

        'name' => [
            'label' => 'Nome',
        ],

        'password' => [
            'label' => 'Palavra-passe',
            'validation_attribute' => 'palavra-passe',
        ],

        'password_confirmation' => [
            'label' => 'Confirmar palavra-passe',
        ],

        'actions' => [

            'register' => [
                'label' => 'Registar conta',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Muitas tentativas de registo',
            'body' => 'Por favor, tente novamente em :seconds segundos.',
        ],

    ],

];
