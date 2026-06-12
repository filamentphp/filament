<?php

return [

    'title' => 'Resetează parola',

    'heading' => 'Ai uitat parola?',

    'actions' => [

        'login' => [
            'label' => 'înapoi la autentificare',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Email',
        ],

        'actions' => [

            'request' => [
                'label' => 'Trimite email',
            ],

        ],

    ],

    'notifications' => [

        'sent' => [
            'body' => 'Dacă contul dumneavoastră nu există, nu veți primi emailul.',
        ],

        'throttled' => [
            'title' => 'Prea multe încercări consecutive',
            'body' => 'Vă rugăm să încercați din nou în :seconds secunde.',
        ],

    ],

];
