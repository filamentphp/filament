<?php

return [

    'title' => 'Login',

    'heading' => 'Prijavite se na svoj račun',

    'buttons' => [

        'submit' => [
            'label' => 'Prijavite se',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'E-mail adresa',
        ],

        'password' => [
            'label' => 'Šifra',
        ],

        'remember' => [
            'label' => 'Zapamti me',
        ],

    ],

    'messages' => [
        'failed' => 'Ovi akreditivi se ne poklapaju sa našom evidencijom.',
        'throttled' => 'TPreviše pokušaja prijave. Pokušajte ponovo za :seconds sekundi.',
    ],

];
