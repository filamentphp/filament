<?php

return [

    'form' => [

        'avatar' => [
            'label' => 'Avatar',
        ],

        'email' => [
            'label' => 'Email adres',
        ],

        'isAdmin' => [
            'label' => 'Filament admin?',
            'helpMessage' => 'Filament admins hebben toegang op alle gebieden van Filament en maakt het beheren van andere gebruikers mogelijk',
        ],

        'isUser' => [
            'label' => 'Filament gebruiker?',
        ],

        'name' => [
            'label' => 'Naam',
        ],

        'password' => [

            'fieldset' => [

                'label' => [
                    'create' => 'Wachtwoord',
                    'edit' => 'Stel nieuw wachtwoord in',
                ],

            ],

            'fields' => [

                'password' => [
                    'label' => 'Wachtwoord',
                ],

                'passwordConfirmation' => [
                    'label' => 'Bevestig wachtwoord',
                ],

            ],

        ],

        'roles' => [
            'label' => 'Rollen',
            'placeholder' => 'Selecteer een rol',
        ],

    ],

    'table' => [

        'columns' => [

            'email' => [
                'label' => 'Email adres',
            ],

            'name' => [
                'label' => 'Naam',
            ],

        ],

        'filters' => [

            'administrators' => [
                'label' => 'Administrators',
            ],

        ],

    ],

];
