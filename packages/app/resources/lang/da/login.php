<?php

return [

    'title' => 'Log ind',

    'heading' => 'Log ind på din konto',

    'buttons' => [

        'submit' => [
            'label' => 'Log ind',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'E-mail',
        ],

        'password' => [
            'label' => 'Adgangskode',
        ],

        'remember' => [
            'label' => 'Husk mig',
        ],

    ],

    'messages' => [
        'failed' => 'Den adgangskode, du har indtastet, er forkert.',
        'throttled' => 'For mange loginforsøg. Prøv venligst igen om :seconds sekunder.',
    ],

];
