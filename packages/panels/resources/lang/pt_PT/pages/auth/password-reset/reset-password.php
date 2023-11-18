<?php

return [

    'title' => 'Redefinir a sua senha',

    'heading' => 'Redefinir a sua senha',

    'form' => [

        'email' => [
            'label' => 'E-mail',
        ],

        'password' => [
            'label' => 'Senha',
            'validation_attribute' => 'senha',
        ],

        'password_confirmation' => [
            'label' => 'Confirmar senha',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Redefinir senha',
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
