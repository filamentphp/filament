<?php

return [

    'title' => 'Tilbakestill ditt passord',

    'heading' => 'Glemt passord?',

    'actions' => [

        'login' => [
            'label' => 'tilbake til logg inn',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-postadresse',
        ],

        'actions' => [

            'request' => [
                'label' => 'Send e-post',
            ],

        ],

    ],

    'notifications' => [

        'sent' => [
            'body' => 'Hvis kontoen din ikke finnes, vil du ikke motta e-posten.',
        ],

        'throttled' => [
            'title' => 'For mange forsøk',
            'body' => 'Vennligst forsøk igjen om :seconds sekunder.',
        ],

    ],

];
