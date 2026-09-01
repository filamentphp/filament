<?php

return [

    'title' => 'Bejelentkezés',

    'heading' => 'Jelentkezz be a fiókodba',

    'actions' => [

        'register' => [
            'before' => 'vagy',
            'label' => 'regisztrálj egy fiókot',
        ],

        'request_password_reset' => [
            'label' => 'Elfelejtetted a jelszavad?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Email cím',
        ],

        'password' => [
            'label' => 'Jelszó',
        ],

        'remember' => [
            'label' => 'Emlékezz rám',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Bejelentkezés',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'Igazold a személyazonosságod',

        'subheading' => 'A bejelentkezés folytatásához igazolnod kell a személyazonosságod.',

        'form' => [

            'provider' => [
                'label' => 'Hogyan szeretnéd igazolni?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Bejelentkezés megerősítése',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Hibás email cím vagy jelszó.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Túl sok bejelentkezési kísérlet',
            'body' => 'Kérjük, próbáld meg újra :seconds másodperc múlva.',
        ],

    ],

];
