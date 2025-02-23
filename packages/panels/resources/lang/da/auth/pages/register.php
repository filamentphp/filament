<?php

return [

    'title' => 'Opret dig',

    'heading' => 'Opret konto',

    'actions' => [

        'login' => [
            'before' => 'eller',
            'label' => 'Log ind på din konto',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-mail',
        ],

        'name' => [
            'label' => 'Navn',
        ],

        'password' => [
            'label' => 'Adgangskode',
            'validation_attribute' => 'adgangskode',
        ],

        'password_confirmation' => [
            'label' => 'Bekræft adgangskode',
        ],

        'actions' => [

            'register' => [
                'label' => 'Opret konto',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'For mange forsøg',
            'body' => 'Prøv igen om :seconds sekunder.',
        ],

    ],

];
