<?php

return [

    'title' => 'Resetujte lozinku',

    'heading' => 'Resetujte lozinku',

    'form' => [

        'email' => [
            'label' => 'Adresa e-pošte',
        ],

        'password' => [
            'label' => 'Lozinka',
            'validation_attribute' => 'lozinka',
        ],

        'password_confirmation' => [
            'label' => 'Potvrdite lozinku',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Resetujte lozinku',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Previše pokušaja resetovanja',
            'body' => 'Pokušajte ponovo za :seconds s.',
        ],

    ],

];
