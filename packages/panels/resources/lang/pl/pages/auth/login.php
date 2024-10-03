<?php

return [

    'title' => 'Logowanie',

    'heading' => 'Zaloguj się',

    'actions' => [

        'register' => [
            'before' => 'lub',
            'label' => 'zarejestruj się',
        ],

        'request_password_reset' => [
            'label' => 'Nie pamiętam hasła',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Adres e-mail',
        ],

        'password' => [
            'label' => 'Hasło',
        ],

        'remember' => [
            'label' => 'Zapamiętaj mnie',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Zaloguj się',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Błędny login lub hasło.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Za dużo nieudanych prób logowania',
            'body' => 'Spróbuj ponownie za :seconds sekund.',
        ],

    ],

];
