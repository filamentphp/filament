<?php

return [

    'form' => [

        'avatar' => [
            'label' => 'Avatar',
        ],

        'email' => [
            'label' => 'Adresse courriel',
        ],

        'isAdmin' => [
            'label' => 'Filament admin?',
            'helpMessage' => 'Les administrateurs de Filament peuvent accéder à toutes les zones de Filament et gérer les autres utilisateurs.',
        ],

        'isUser' => [
            'label' => 'Filament utilisateur?',
        ],

        'name' => [
            'label' => 'Nom',
        ],

        'password' => [

            'fieldset' => [

                'label' => [
                    'create' => 'Mot de passe',
                    'edit' => 'Définissez un nouveau mot de passe',
                ],

            ],

            'fields' => [

                'password' => [
                    'label' => 'Mot de passe',
                ],

                'passwordConfirmation' => [
                    'label' => 'Confirmez le mot de passe',
                ],

            ],

        ],

        'roles' => [
            'label' => 'Rôles',
            'placeholder' => 'Sélectionnez un rôle',
        ],

    ],

    'table' => [

        'columns' => [

            'email' => [
                'label' => 'Addresse courriel',
            ],

            'name' => [
                'label' => 'Nom',
            ],

        ],

        'filters' => [

            'administrators' => [
                'label' => 'Administrateurs',
            ],

        ],

    ],

];
