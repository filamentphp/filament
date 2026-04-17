<?php

return [

    'title' => 'Sisselogimine',

    'heading' => 'Logige sisse',

    'actions' => [

        'register' => [
            'before' => 'või',
            'label' => 'registreeruge konto jaoks',
        ],

        'request_password_reset' => [
            'label' => 'Unustasite parooli?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-posti aadress',
        ],

        'password' => [
            'label' => 'Parool',
        ],

        'remember' => [
            'label' => 'Jäta meelde',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Logige sisse',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'Kinnitage oma identiteet',

        'subheading' => 'Sisselogimise jätkamiseks peate kinnitama oma identiteedi.',

        'form' => [

            'provider' => [
                'label' => 'Kuidas soovite kinnitada?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Kinnitage sisselogimine',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Need mandaadid ei vasta meie andmetele.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Liiga palju sisselogimise katseid',
            'body' => 'Palun proovige uuesti :seconds sekundi pärast.',
        ],

    ],

];
