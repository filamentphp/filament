<?php

return [

    'title' => 'Obnovte svoje heslo',

    'heading' => 'Zabudli ste svoje heslo?',

    'actions' => [

        'login' => [
            'label' => 'späť na prihlásenie',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Emailová adresa',
        ],

        'actions' => [

            'request' => [
                'label' => 'Odoslať email',
            ],

        ],

    ],

    'notifications' => [

        'sent' => [
            'body' => 'Ak Váš účet neexistuje, e-mail nedostanete.',
        ],

        'throttled' => [
            'title' => 'Príliš veľa pokusov',
            'body' => 'Prosím skúste to znovu o :seconds sekúnd.',
        ],

    ],

];
