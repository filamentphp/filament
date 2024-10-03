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

    'messages' => [

        'failed' => 'Hibás email cím vagy jelszó.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Túl sok bejelentkezési kísérlet',
            'body' => 'Kérjük, próbáld meg újra :second másodperc múlva.',
        ],

    ],

];
