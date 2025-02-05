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
            'label' => 'Unustasid salasõna?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-posti aadress',
        ],

        'password' => [
            'label' => 'Salasõna',
        ],

        'remember' => [
            'label' => 'Jäta mind meelde',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Logi sisse',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Need andmed ei vasta meie andmetele.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Liiga palju sisselogimise katseid',
            'body' => 'Palun proovi uuesti :seconds sekundi pärast.',
        ],

    ],

];
