<?php

return [

    'title' => 'Přihlášení',

    'heading' => 'Přihlašte se k Vašemu účtu',

    'actions' => [

        'register' => [
            'before' => 'nebo',
            'label' => 'se zaregistrujte',
        ],

        'request_password_reset' => [
            'label' => 'Zapomněli jste heslo?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Emailová adresa',
        ],

        'password' => [
            'label' => 'Heslo',
        ],

        'remember' => [
            'label' => 'Zapamatovat si mě',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Přihlásit se',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Chybně zadané přihlašovací údaje.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Příliš mnoho pokusů o přihlášení.',
            'body' => 'Zkuste to znovu za :seconds vteřin.',
        ],

    ],

];
