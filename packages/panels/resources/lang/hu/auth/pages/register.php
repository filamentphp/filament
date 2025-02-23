<?php

return [

    'title' => 'Regisztráció',

    'heading' => 'Regisztrálj új fiókot',

    'actions' => [

        'login' => [
            'before' => 'vagy',
            'label' => 'jelentkezz be a fiókodba',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Email cím',
        ],

        'name' => [
            'label' => 'Név',
        ],

        'password' => [
            'label' => 'Jelszó',
            'validation_attribute' => 'jelszó',
        ],

        'password_confirmation' => [
            'label' => 'Jelszó megerősítése',
        ],

        'actions' => [

            'register' => [
                'label' => 'Regisztrálok',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Túl sok regisztrációs kísérlet',
            'body' => 'Kérjük, próbáld meg újra :seconds másodperc múlva.',
        ],

    ],

];
