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

    'multi_factor' => [

        'heading' => 'Ověřte svou identitu',

        'subheading' => 'Pro pokračování přihlášení musíte ověřit svou identitu.',

        'form' => [

            'provider' => [
                'label' => 'Jak si přejete ověřit svou identitu?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Potvrdit přihlášení',
                ],

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
