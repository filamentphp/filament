<?php

return [

    'title' => 'Tilbakestill passord',

    'heading' => 'Tilbakestill passord',

    'form' => [

        'email' => [
            'label' => 'E-postadresse',
        ],

        'password' => [
            'label' => 'Passord',
            'validation_attribute' => 'passord',
        ],

        'password_confirmation' => [
            'label' => 'Bekreft passord',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Tilbakestill passord',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'For mange forsøkt på tilbakestilling av passord',
            'body' => 'Vennligst forsøk igjen om :seconds sekunder.',
        ],

    ],

];
