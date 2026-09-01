<?php

return [

    'title' => 'Resetujte lozinku',

    'heading' => 'Zaboravili ste lozinku?',

    'actions' => [

        'login' => [
            'label' => 'nazad na prijavu',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Adresa e-pošte',
        ],

        'actions' => [

            'request' => [
                'label' => 'Pošalji poruku',
            ],

        ],

    ],

    'notifications' => [

        'sent' => [
            'body' => 'Ako vaš nalog ne postoji nećete dobiti poruku e-poštom.',
        ],

        'throttled' => [
            'title' => 'Previše slanja',
            'body' => 'Pokušajte ponovo za :seconds s.',
        ],

    ],

];
