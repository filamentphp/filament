<?php

return [

    'title' => 'Connexion',

    'heading' => 'Connectez-vous à votre compte',

    'actions' => [

        'register' => [
            'before' => 'ou',
            'label' => 'créer un compte',
        ],

        'request_password_reset' => [
            'label' => 'Mot de passe oublié ?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Adresse e-mail',
        ],

        'password' => [
            'label' => 'Mot de passe',
        ],

        'remember' => [
            'label' => 'Se souvenir de moi',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Connexion',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'Vérifier votre identité',

        'subheading' => 'Pour continuer à vous connecter, vous devez vérifier votre identité.',

        'form' => [

            'provider' => [
                'label' => 'Comment souhaitez-vous vérifier ?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Confirmer la connexion',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Ces identifiants ne correspondent pas à nos enregistrements.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Trop de tentatives de connexion',
            'body' => 'Merci de réessayer dans :seconds secondes.',
        ],

    ],

];
