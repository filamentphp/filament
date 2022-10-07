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
        'failed' => 'Vaša kombinacija se ne poklapa sa našom evidencijom.',
        'throttled' => 'Previše pokušaja prijave. Pokušajte ponovo za :seconds sekundi.',
    ],

];
