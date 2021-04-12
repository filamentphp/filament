<?php

return [

    'form' => [

        'avatar' => [
            'label' => 'Avatar',
        ],

        'email' => [
            'label' => 'Email adress',
        ],

        'isAdmin' => [
            'label' => 'Filament admin?',
            'helpMessage' => 'Filament administratörer kan hantera användare och har tillgång till alla delar av Filament.',
        ],

        'isUser' => [
            'label' => 'Filament användare?',
        ],

        'name' => [
            'label' => 'Namn',
        ],

        'password' => [

            'fieldset' => [

                'label' => [
                    'create' => 'Lösenord',
                    'edit' => 'Ange nytt lösenord',
                ],

            ],

            'fields' => [

                'password' => [
                    'label' => 'Lösenord',
                ],

                'passwordConfirmation' => [
                    'label' => 'Bekräfta lösenord',
                ],

            ],

        ],

        'roles' => [
            'label' => 'Roles',
            'placeholder' => 'Select a role',
        ],

    ],

    'table' => [

        'columns' => [

            'email' => [
                'label' => 'Email address',
            ],

            'name' => [
                'label' => 'Name',
            ],

        ],

        'filters' => [

            'administrators' => [
                'label' => 'Administrators',
            ],

        ],

    ],

];
