<?php

return [

    'title' => 'Registrácia',

    'heading' => 'Registrujte sa',

    'actions' => [

        'login' => [
            'before' => 'alebo',
            'label' => 'sa prihláste do svojho účtu',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Emailová adresa',
        ],

        'name' => [
            'label' => 'Meno',
        ],

        'password' => [
            'label' => 'Heslo',
            'validation_attribute' => 'heslo',
        ],

        'password_confirmation' => [
            'label' => 'Potvrdenie hesla',
        ],

        'actions' => [

            'register' => [
                'label' => 'Prihlásiť sa',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Príliš veľa pokusov o registráciu',
            'body' => 'Prosím vyskúšajte to o :seconds sekúnd.',
        ],

    ],

];
