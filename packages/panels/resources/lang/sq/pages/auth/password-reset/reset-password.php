<?php

return [

    'title' => 'Rivendosni fjalëkalimin tuaj',

    'heading' => 'Rivendosni fjalëkalimin tuaj',

    'form' => [

        'email' => [
            'label' => 'Adresa e emailit',
        ],

        'password' => [
            'label' => 'Fjalëkalimi',
            'validation_attribute' => 'fjalëkalimi',
        ],

        'password_confirmation' => [
            'label' => 'Konfirmo fjalëkalimin',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Rivendosni fjalëkalimin',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Shumë përpjekje për rivendosje',
            'body' => 'Ju lutemi provoni përsëri në :seconds sekonda.',
        ],

    ],

];
