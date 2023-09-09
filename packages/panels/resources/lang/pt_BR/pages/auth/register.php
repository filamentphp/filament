<?php

return [

    'title' => 'Cadastrar',

    'heading' => 'Inscrever-se',

    'actions' => [

        'login' => [
            'before' => 'ou',
            'label' => 'faça login em sua conta',
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
            'validation_attribute' => 'password',
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
            'title' => 'Muitas tentativas de cadastro',
            'body' => 'Por favor tente novamente em :seconds segundos.',
        ],

    ],

];
