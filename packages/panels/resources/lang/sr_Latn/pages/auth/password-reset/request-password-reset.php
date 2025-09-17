<?php

return [

    'title' => 'Resetuj svoju lozinku',

    'heading' => 'Zaboravljena lozinka?',

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
                'label' => 'Pošalji e-poštu',
            ],

        ],

    ],

    'notifications' => [

        'sent' => [
            'body' => 'Ako vaš nalog ne postoji, nećete dobiti e-poštu.',
        ],

        'throttled' => [
            'title' => 'Previše zahteva',
            'body' => 'Molim vas, pokušajte ponovo za :seconds sekundi.',
        ],

    ],

];
