<?php

return [

    'title' => 'Réinitialiser votre mot de passe',

    'heading' => 'Réinitialiser votre mot de passe',

    'form' => [

        'email' => [
            'label' => 'Adresse Email',
        ],

        'password' => [
            'label' => 'Mot de passe',
            'validation_attribute' => 'mot de passe',
        ],

        'password_confirmation' => [
            'label' => 'Confirmer le mot de passe',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Réinitialiser le mot de passe',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Trop de tentatives de réinitialisation',
            'body' => 'Merci de réessayer dans :seconds secondes.',
        ],

    ],

];
