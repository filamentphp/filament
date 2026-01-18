<?php

return [

    'title' => 'Resetiraj ja tvojata lozinka',

    'heading' => 'Resetiraj ja tvojata lozinka',

    'form' => [

        'email' => [
            'label' => 'E-pošta adresa',
        ],

        'password' => [
            'label' => 'Lozinka',
            'validation_attribute' => 'lozinka',
        ],

        'password_confirmation' => [
            'label' => 'Potvrdi lozinka',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Resetiraj lozinka',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Premnogu obidi za resetiranje',
            'body' => 'Ve molime obidete se povtorno za :seconds sekundi.',
        ],

    ],

];
