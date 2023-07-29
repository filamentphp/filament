<?php

return [

    'title' => 'Log ind',

    'heading' => 'Log ind på din konto',

    'form' => [

        'email' => [
            'label' => 'E-mail',
        ],

        'password' => [
            'label' => 'Adgangskode',
        ],

        'remember' => [
            'label' => 'Husk mig',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Log ind',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Den adgangskode, du har indtastet, er forkert.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'For mange loginforsøg. Prøv venligst igen om :seconds sekunder.',
        ],

    ],

];
