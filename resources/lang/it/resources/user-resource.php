<?php

return [

    'form' => [

        'avatar' => [
            'label' => 'Avatar',
        ],

        'email' => [
            'label' => 'Indirizzo email',
        ],

        'isAdmin' => [
            'label' => 'Admin Filament?',
            'helpMessage' => 'Gli Admin Filament sono in grado di accedere a tutte le aree di Filament e gestire gli altri utenti.',
        ],

        'isUser' => [
            'label' => 'Utente Filament?',
        ],

        'name' => [
            'label' => 'Nome',
        ],

        'password' => [

            'fieldset' => [

                'label' => [
                    'create' => 'Password',
                    'edit' => 'Imposta una nuova password',
                ],

            ],

            'fields' => [

                'password' => [
                    'label' => 'Password',
                ],

                'passwordConfirmation' => [
                    'label' => 'Conferma password',
                ],

            ],

        ],

        'roles' => [
            'label' => 'Ruoli',
            'placeholder' => 'Seleziona un ruolo',
        ],

    ],

    'table' => [

        'columns' => [

            'email' => [
                'label' => 'Indirizzo email',
            ],

            'name' => [
                'label' => 'Nome',
            ],

        ],

        'filters' => [

            'administrators' => [
                'label' => 'Amministratori',
            ],

        ],

    ],

];
