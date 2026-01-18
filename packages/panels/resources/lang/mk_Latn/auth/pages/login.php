<?php

return [

    'title' => 'Najava',

    'heading' => 'Najavi se',

    'actions' => [

        'register' => [
            'before' => 'ili',
            'label' => 'registriraj smetka',
        ],

        'request_password_reset' => [
            'label' => 'Zaboravena lozinka?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-pošta adresa',
        ],

        'password' => [
            'label' => 'Lozinka',
        ],

        'remember' => [
            'label' => 'Zapomni me',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Najavi se',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'Potvrdi go tvojot identitet',

        'subheading' => 'Za da prodolžiš so najavuvanjeto, treba da go potvrdiš tvojot identitet.',

        'form' => [

            'provider' => [
                'label' => 'Kako bi sakal da potvrdiš?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Potvrdi najava',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Ovie podatoci ne se sovpаѓaat so našite zapisi.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Premnogu obidi za najava',
            'body' => 'Ve molime obidete se povtorno za :seconds sekundi.',
        ],

    ],

];
