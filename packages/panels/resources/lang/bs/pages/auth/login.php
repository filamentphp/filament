<?php

return [

    'title' => 'Login',

    'heading' => 'Prijavite se na svoj račun',

    'form' => [

        'email' => [
            'label' => 'E-mail adresa',
        ],

        'password' => [
            'label' => 'Šifra',
        ],

        'remember' => [
            'label' => 'Zapamti me',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Prijavite se',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Vaša kombinacija se ne poklapa sa našom evidencijom.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Previše pokušaja prijave. Pokušajte ponovo za :seconds sekundi.',
        ],

    ],

];
