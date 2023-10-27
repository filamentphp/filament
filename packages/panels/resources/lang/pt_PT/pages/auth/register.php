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
            'label' => 'E-mail',
        ],

        'name' => [
            'label' => 'Nome',
        ],

        'password' => [
            'label' => 'Senha',
            'validation_attribute' => 'senha',
        ],

        'password_confirmation' => [
            'label' => 'Confirmar senha',
        ],

        'actions' => [

            'register' => [
                'label' => 'Criar conta',
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
