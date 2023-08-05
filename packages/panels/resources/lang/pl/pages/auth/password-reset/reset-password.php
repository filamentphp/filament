<?php

return [

    'title' => 'Zresetuj hasło',

    'heading' => 'Resetowanie hasła',

    'form' => [

        'email' => [
            'label' => 'Adres e-mail',
        ],

        'password' => [
            'label' => 'Hasło',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Potwierdź hasło',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Zresetuj hasło',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Zbyt wiele prób resetowania',
            'body' => 'Spróbuj ponownie za :seconds sekund.',
        ],

    ],

];
