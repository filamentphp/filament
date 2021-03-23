<?php

return [

    'form' => [

        'avatar' => [
            'label' => 'Avatar',
        ],

        'email' => [
            'label' => 'Adreça de correu electrònic',
        ],

        'isAdmin' => [
            'label' => ' Administrador Filament?',
            'helpMessage' => 'Els administradors de Filament poden accedir a totes les àrees de Filament i gestionar altres usuaris.',
        ],

        'isUser' => [
            'label' => 'Usuari Filament?',
        ],

        'name' => [
            'label' => 'Nom',
        ],

        'password' => [

            'fieldset' => [

                'label' => [
                    'create' => 'Contrasenya',
                    'edit' => 'Establir una nova contrasenya',
                ],

            ],

            'fields' => [

                'password' => [
                    'label' => 'Contrasenya',
                ],

                'passwordConfirmation' => [
                    'label' => 'Confirmació de la contrasenya',
                ],

            ],

        ],

        'roles' => [
            'label' => 'Rols',
            'placeholder' => 'Seleccioneu un rol',
        ],

    ],

    'table' => [

        'columns' => [

            'email' => [
                'label' => 'Correu electrònic',
            ],

            'name' => [
                'label' => 'Nom',
            ],

        ],

        'filters' => [

            'administrators' => [
                'label' => 'Administradors',
            ],

        ],

    ],

];
