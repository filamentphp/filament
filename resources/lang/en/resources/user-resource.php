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

        'newPassword' => [

            'fieldset' => [
                'label' => 'Set a new password',
            ],

            'fields' => [

                'newPassword' => [
                    'label' => 'Password',
                ],

                'newPasswordConfirmation' => [
                    'label' => 'Confirm password',
                ],

            ],

        ],

        'password' => [
            'label' => 'Password',
        ],

        'passwordConfirmation' => [
            'label' => 'Confirm password',
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
