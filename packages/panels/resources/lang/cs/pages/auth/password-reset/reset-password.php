<?php

return [

    'title' => 'Nastavte si nové heslo',

    'heading' => 'Nastavte si nové heslo',

    'form' => [

        'email' => [
            'label' => 'E-mailová adresa',
        ],

        'password' => [
            'label' => 'Heslo',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Potvrďte heslo',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Nastavit nové heslo',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Příliš mnoho požadavků',
            'body' => 'Zkuste to prosím znovu za :seconds sekund.',
        ],

    ],

];
