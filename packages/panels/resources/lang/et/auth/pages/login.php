<?php

return [

    'title' => 'Logi sisse',

    'heading' => 'Logi sisse',

    'actions' => [

        'register' => [
            'before' => 'või',
            'label' => 'loo uus konto',
        ],

        'request_password_reset' => [
            'label' => 'Unustasid parooli?',
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
                'label' => 'Logi sisse',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'Kinnita oma identiteet',

        'subheading' => 'Sisselogimise jätkamiseks peate oma identiteedi kinnitama.',

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
