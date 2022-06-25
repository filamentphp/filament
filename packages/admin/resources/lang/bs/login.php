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
        'failed' => 'These credentials do not match our records.',
        'throttled' => 'Too many login attempts. Please try again in :seconds seconds.',
    ],

];
