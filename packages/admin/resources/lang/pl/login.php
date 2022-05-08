<?php

return [

    'title' => 'Logowanie',

    'heading' => 'Zaloguj się',

    'buttons' => [

        'submit' => [
            'label' => 'Zaloguj się',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'Adres e-mail',
        ],

        'password' => [
            'label' => 'Hasło',
        ],

        'remember' => [
            'label' => 'Zapamiętaj mnie',
        ],

    ],

    'messages' => [
        'failed' => 'Nieprawidłowe poświadczenia.',
        'throttled' => 'Zbyt wiele nieudanych prób logowania. Spróbuj ponownie za :seconds sekund.',
    ],

];
