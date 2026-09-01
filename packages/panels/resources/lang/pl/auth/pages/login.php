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

    'multi_factor' => [

        'heading' => 'Zweryfikuj swoją tożsamość',

        'subheading' => 'Aby kontynuować logowanie, musisz zweryfikować swoją tożsamość.',

        'form' => [

            'provider' => [
                'label' => 'Wybierz metodę weryfikacji',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Potwierdź logowanie',
                ],

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
