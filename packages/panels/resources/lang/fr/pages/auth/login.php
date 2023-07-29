<?php

return [

    'title' => 'Connexion',

    'heading' => 'Connectez-vous à votre compte',

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

    'messages' => [

        'failed' => 'Ces identifiants ne correspondent pas à nos enregistrements.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Tentatives de connexion trop nombreuses. Veuillez essayer de nouveau dans :seconds secondes.',
        ],

    ],

];
