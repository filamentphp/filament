<?php

return [

    'title' => 'Jelszó visszaállítás',

    'heading' => 'Jelszó visszaállítás',

    'form' => [

        'email' => [
            'label' => 'Email cím',
        ],

        'password' => [
            'label' => 'Jelszó',
            'validation_attribute' => 'jelszó',
        ],

        'password_confirmation' => [
            'label' => 'Jelszó megerősítése',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Jelszó visszaállítása',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Túl sok visszaállítási kísérlet',
            'body' => 'Kérjük, próbáld meg újra :second másodperc múlva.',
        ],

    ],

];
