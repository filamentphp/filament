<?php

return [

    'title' => 'Přihlášení',

    'heading' => 'Přihlašte se k Vašemu účtu',

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
            'title' => 'Příliš mnoho pokusů o přihlášení. Zkuste to znovu za :seconds vteřin.',
        ],

    ],

];
