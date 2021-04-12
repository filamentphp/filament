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
            'label' => 'Roller',
            'placeholder' => 'Välj en roll',
        ],

    ],

    'table' => [

        'columns' => [

            'email' => [
                'label' => 'Email',
            ],

            'name' => [
                'label' => 'Namn',
            ],

        ],

        'filters' => [

            'administrators' => [
                'label' => 'Administratörer',
            ],

        ],

    ],

];
