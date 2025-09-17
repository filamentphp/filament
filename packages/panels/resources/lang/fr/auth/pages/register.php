<?php

return [

    'title' => "S'inscrire",

    'heading' => "S'inscrire",

    'actions' => [

        'login' => [
            'before' => 'ou',
            'label' => 'connectez-vous à votre compte',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Adresse Email',
        ],

        'name' => [
            'label' => 'Nom',
        ],

        'password' => [
            'label' => 'Mot de passe',
            'validation_attribute' => 'mot de passe',
        ],

        'password_confirmation' => [
            'label' => 'Confirmer le mot de passe',
        ],

        'actions' => [

            'register' => [
                'label' => "S'inscrire",
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => "Trop de tentatives d'inscription",
            'body' => 'Merci de réessayer dans :seconds secondes.',
        ],

    ],

];
