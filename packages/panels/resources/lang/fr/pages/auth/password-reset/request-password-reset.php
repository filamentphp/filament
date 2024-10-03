<?php

return [

    'title' => 'Réinitialiser votre mot de passe',

    'heading' => 'Mot de passe oublié ?',

    'actions' => [

        'login' => [
            'label' => 'retour à la connexion',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Adresse Email',
        ],

        'actions' => [

            'request' => [
                'label' => "Envoyer l'email",
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Trop de requêtes',
            'body' => 'Merci de réessayer dans :seconds secondes.',
        ],

    ],

];
