<?php

return [

    'title' => 'Logga in',

    'heading' => 'Logga in',

    'actions' => [

        'register' => [
            'before' => 'eller',
            'label' => 'skapa ett konto',
        ],

        'request_password_reset' => [
            'label' => 'Glömt ditt lösenord?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Mejladress',
        ],

        'password' => [
            'label' => 'Lösenord',
        ],

        'remember' => [
            'label' => 'Kom ihåg mig',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Logga in',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Inloggningsuppgifterna matchar inte våra register.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'För många inloggningsförsök',
            'body' => 'Vänligen försök igen om :seconds sekunder.',
        ],

    ],

];
