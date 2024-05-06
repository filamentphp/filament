<?php

return [

    'title' => 'Ponastavite svoje geslo',

    'heading' => 'Ponastavite svoje geslo',

    'form' => [

        'email' => [
            'label' => 'E-poštni naslov',
        ],

        'password' => [
            'label' => 'Geslo',
            'validation_attribute' => 'geslo',
        ],

        'password_confirmation' => [
            'label' => 'Potrdite geslo',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Ponastavi geslo',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Preveč poskusov ponastavitve',
            'body' => 'Poskusite znova čez :seconds sekund.',
        ],

    ],

];
