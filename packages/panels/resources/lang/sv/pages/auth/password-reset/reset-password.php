<?php

return [

    'title' => 'Återställ ditt lösenord',

    'heading' => 'Återställ ditt lösenord',

    'form' => [

        'email' => [
            'label' => 'Mejladress',
        ],

        'password' => [
            'label' => 'Lösenord',
            'validation_attribute' => 'lösenord',
        ],

        'password_confirmation' => [
            'label' => 'Bekräfta lösenord',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Återställ lösenord',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'För många förfrågningar om återställning',
            'body' => 'Vänligen försök igen om :seconds sekunder.',
        ],

    ],

];
