<?php

return [

    'form' => [

        'avatar' => [
            'label' => 'Avatar',
        ],

        'email' => [
            'label' => 'Email adresse',
        ],

        'isAdmin' => [
            'label' => 'Filament admin?',
            'helpMessage' => 'Filament administratorer har adgang til alle områder af Filament og administrere andre brugere.',
        ],

        'isUser' => [
            'label' => 'Filament bruger?',
        ],

        'name' => [
            'label' => 'Navn',
        ],

        'password' => [

            'fieldset' => [

                'label' => [
                    'create' => 'Adgangskode',
                    'edit' => 'Indstil en ny adgangskode',
                ],

            ],

            'fields' => [

                'password' => [
                    'label' => 'Adgangskode',
                ],

                'passwordConfirmation' => [
                    'label' => 'Bekræft adgangskode',
                ],

            ],

        ],

        'roles' => [
            'label' => 'Roller',
            'placeholder' => 'Vælg en rolle',
        ],

    ],

    'table' => [

        'columns' => [

            'email' => [
                'label' => 'Email adresse',
            ],

            'name' => [
                'label' => 'Navn',
            ],

        ],

        'filters' => [

            'administrators' => [
                'label' => 'Administratorer',
            ],

        ],

    ],

];
