<?php

return [

    'form' => [

        'avatar' => [
            'label' => 'Avatar',
        ],

        'email' => [
            'label' => 'Correo electrónico',
        ],

        'isAdmin' => [
            'label' => '¿Administrador de Filament?',
            'helpMessage' => 'Los administradores de Filament pueden acceder a todas las áreas de Filament y gestionar otros usuarios.',
        ],

        'isUser' => [
            'label' => '¿Usuario de Filament?',
        ],

        'name' => [
            'label' => 'Nombre',
        ],

        'password' => [

            'fieldset' => [

                'label' => [
                    'create' => 'Contraseña',
                    'edit' => 'Establecer una nueva contraseña',
                ],

            ],

            'fields' => [

                'password' => [
                    'label' => 'Contraseña',
                ],

                'passwordConfirmation' => [
                    'label' => 'Confirmar contraseña',
                ],

            ],

        ],

        'roles' => [
            'label' => 'Roles',
            'placeholder' => 'Seleccionar un rol',
        ],

    ],

    'table' => [

        'columns' => [

            'email' => [
                'label' => 'Correo electrónico',
            ],

            'name' => [
                'label' => 'Nombre',
            ],

        ],

        'filters' => [

            'administrators' => [
                'label' => 'Administradores',
            ],

        ],

    ],

];
