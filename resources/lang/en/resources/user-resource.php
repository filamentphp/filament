<?php

return [

    'form' => [

        'avatar' => [
            'label' => 'Avatar',
        ],

        'email' => [
            'label' => 'Email address',
        ],

        'isAdmin' => [
            'label' => 'Filament admin?',
            'helpMessage' => 'Filament admins are able to access all areas of Filament and manage other users.',
        ],

        'isUser' => [
            'label' => 'Filament user?',
        ],

        'name' => [
            'label' => 'Name',
        ],

        'password' => [

            'fieldset' => [

                'label' => [
                    'create' => 'Password',
                    'edit' => 'Set a new password',
                ],

            ],

            'fields' => [

                'password' => [
                    'label' => 'Password',
                ],

                'passwordConfirmation' => [
                    'label' => 'Confirm password',
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
