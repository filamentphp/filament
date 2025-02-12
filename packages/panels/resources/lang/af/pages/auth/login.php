<?php

return [

    'title' => 'Teken in',

    'heading' => 'Meld aan',

    'actions' => [

        'register' => [
            'before' => 'of',
            'label' => 'teken aan vir \'n rekening',
        ],

        'request_password_reset' => [
            'label' => 'Wagwoord vergeet?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-pos adres',
        ],

        'password' => [
            'label' => 'Wagwoord',
        ],

        'remember' => [
            'label' => 'Onthou my',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Meld aan',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Hierdie geloofsbriewe stem nie ooreen met ons rekords nie.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Te veel aanmeldpogings',
            'body' => 'Probeer asseblief weer oor :seconds sekondes.',
        ],

    ],

];
