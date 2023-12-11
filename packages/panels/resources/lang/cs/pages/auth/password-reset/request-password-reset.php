<?php

return [

    'title' => 'Obnovení hesla',

    'heading' => 'Zapomněli jste heslo?',

    'actions' => [

        'login' => [
            'label' => 'zpět na přihlášení',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-mailová adresa',
        ],

        'actions' => [

            'request' => [
                'label' => 'Odeslat odkaz na obnovení hesla',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Příliš mnoho požadavků',
            'body' => 'Zkuste to prosím znovu za :seconds sekund.',
        ],

    ],

];
