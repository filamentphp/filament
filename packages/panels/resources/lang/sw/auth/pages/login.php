<?php

return [

    'title' => 'Ingia',

    'heading' => 'Ingia kwenye akaunti yako',

    'actions' => [
        'register' => [
            'before' => 'au',
            'label' => 'jisajili akaunti',
        ],

        'request_password_reset' => [
            'label' => 'Umesahau nenosiri?',
        ],
    ],

    'form' => [
        'email' => [
            'label' => 'Barua pepe',
        ],

        'password' => [
            'label' => 'Nenosiri',
        ],

        'remember' => [
            'label' => 'Nikumbuke',
        ],

        'actions' => [
            'authenticate' => [
                'label' => 'Ingia',
            ],
        ],
    ],

    'multi_factor' => [
        'heading' => 'Thibitisha utambulisho wako',

        'subheading' => 'Ili kuendelea kuingia, unahitaji kuthibitisha utambulisho wako.',

        'form' => [
            'provider' => [
                'label' => 'Ungependa kuthibitisha kwa njia gani?',
            ],

            'actions' => [
                'authenticate' => [
                    'label' => 'Thibitisha kuingia',
                ],
            ],
        ],
    ],

    'messages' => [
        'failed' => 'Hati hizi hazilingani na rekodi zetu.',
    ],

    'notifications' => [
        'throttled' => [
            'title' => 'Majaribio mengi sana ya kuingia. Tafadhali jaribu tena ndani ya sekunde :seconds.',
            'body' => 'Tafadhali jaribu tena baada ya sekunde :seconds.',
        ],
    ],

];
