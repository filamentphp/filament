<?php

return [

    'title' => 'Registriraj se',

    'heading' => 'Registriraj se',

    'actions' => [

        'login' => [
            'before' => 'ili',
            'label' => 'najavi se na tvojata smetka',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-pošta adresa',
        ],

        'name' => [
            'label' => 'Ime',
        ],

        'password' => [
            'label' => 'Lozinka',
            'validation_attribute' => 'lozinka',
        ],

        'password_confirmation' => [
            'label' => 'Potvrdi lozinka',
        ],

        'actions' => [

            'register' => [
                'label' => 'Registriraj se',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Premnogu obidi za registracija',
            'body' => 'Ve molime obidete se povtorno za :seconds sekundi.',
        ],

    ],

];
