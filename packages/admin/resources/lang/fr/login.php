<?php

return [

    'title' => 'Connexion',

    'heading' => 'Connectez-vous à votre compte',

    'buttons' => [

        'submit' => [
            'label' => 'Connexion',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'Adresse e-mail',
        ],

        'password' => [
            'label' => 'Mot de passe',
        ],

        'remember' => [
            'label' => 'Se souvenir de moi',
        ],

    ],

    'messages' => [
        'failed' => 'Ces identifiants ne correspondent pas à nos enregistrements.',
        'throttled' => 'Tentatives de connexion trop nombreuses. Veuillez essayer de nouveau dans :seconds secondes.',
    ],

];
