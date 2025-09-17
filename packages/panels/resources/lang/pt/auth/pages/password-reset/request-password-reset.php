<?php

return [

    'title' => 'Redefinir a sua palavra-passe',

    'heading' => 'Esqueceu-se da palavra-passe?',

    'actions' => [

        'login' => [
            'label' => 'voltar ao início',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Endereço de e-mail',
        ],

        'actions' => [

            'request' => [
                'label' => 'Enviar e-mail',
            ],

        ],

    ],

    'notifications' => [

        'sent' => [
            'body' => 'Se a sua conta não existir, não receberá o e-mail.',
        ],

        'throttled' => [
            'title' => 'Muitas solicitações',
            'body' => 'Por favor, tente novamente em :seconds segundos.',
        ],

    ],

];
