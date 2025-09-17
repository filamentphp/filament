<?php

return [

    'title' => 'Obnovte svoje heslo',

    'heading' => 'Obnovte svoje heslo',

    'form' => [

        'email' => [
            'label' => 'Emailová adresa',
        ],

        'password' => [
            'label' => 'Heslo',
            'validation_attribute' => 'heslo',
        ],

        'password_confirmation' => [
            'label' => 'Potvrdenie hesla',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Obnoviť heslo',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Príliš veľa pokusov',
            'body' => 'Prosím skúste to znovu o :seconds sekúnd.',
        ],

    ],

];
