<?php

return [

    'title' => 'Regisztráció',

    'heading' => 'Regisztráció',

    'actions' => [

        'login' => [
            'before' => 'vagy',
            'label' => 'jelentkezzen be a fiókjába',
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
            'body' => 'Kérjük, próbálja meg újra :second másodperc múlva.',
        ],

    ],

];
