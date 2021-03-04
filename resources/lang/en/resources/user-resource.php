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
            'label' => 'Administrator?',
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
