<?php

return [

    'form' => [

        'avatar' => [
            'label' => 'Avatar',
        ],

        'email' => [
            'label' => 'E-Mail Adresse',
        ],

        'isAdmin' => [
            'label' => 'Filament Administrator?',
            'helpMessage' => 'Filament Administratoren können auf alle Bereiche von Filament zugreifen und andere Benutzer verwalten.',
        ],

        'isUser' => [
            'label' => 'Filament Benutzer?',
        ],

        'name' => [
            'label' => 'Name',
        ],

        'password' => [

            'fieldset' => [

                'label' => [
                    'create' => 'Passwort',
                    'edit' => 'Ein neues Passwort festlegen',
                ],

            ],

            'fields' => [

                'password' => [
                    'label' => 'Passwort',
                ],

                'passwordConfirmation' => [
                    'label' => 'Passwort bestätigen',
                ],

            ],

        ],

        'roles' => [
            'label' => 'Rollen',
            'placeholder' => 'Rolle auswählen',
        ],

    ],

    'table' => [

        'columns' => [

            'email' => [
                'label' => 'E-Mail Adresse',
            ],

            'name' => [
                'label' => 'Name',
            ],

        ],

        'filters' => [

            'administrators' => [
                'label' => 'Administratoren',
            ],

        ],

    ],

];
