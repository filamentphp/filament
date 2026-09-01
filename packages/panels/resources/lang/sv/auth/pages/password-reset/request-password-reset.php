<?php

return [

    'title' => 'Återställ ditt lösenord',

    'heading' => 'Glömt ditt lösenord?',

    'actions' => [

        'login' => [
            'label' => 'tillbaka till inloggningen',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Mejladress',
        ],

        'actions' => [

            'request' => [
                'label' => 'Skicka meddelande',
            ],

        ],

    ],

    'notifications' => [

        'sent' => [
            'body' => 'Om ditt konto inte finns, kommer du inte att få något meddelande.',
        ],

        'throttled' => [
            'title' => 'För många förfrågningar',
            'body' => 'Vänligen försök igen om :seconds sekunder.',
        ],

    ],

];
