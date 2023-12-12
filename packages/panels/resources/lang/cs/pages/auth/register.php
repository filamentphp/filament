<?php

return [

    'title' => 'Registrace',

    'heading' => 'Zaregistrovat se',

    'actions' => [

        'login' => [
            'before' => 'nebo',
            'label' => 'přihlásit se ke svému účtu',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-mailová adresa',
        ],

        'name' => [
            'label' => 'Jméno',
        ],

        'password' => [
            'label' => 'Heslo',
            'validation_attribute' => 'Heslo',
        ],

        'password_confirmation' => [
            'label' => 'Potvrďte heslo',
        ],

        'actions' => [

            'register' => [
                'label' => 'Zaregistrovat se',
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
