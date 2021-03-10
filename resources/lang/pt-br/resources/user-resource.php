<?php

return [

    'form' => [

        'avatar' => [
            'label' => 'Avatar',
        ],

        'email' => [
            'label' => 'E-mail',
        ],

        'isAdmin' => [
            'label' => 'Admin Filament?',
            'helpMessage' => 'Os administradores do Filament podem acessar todas as áreas do Filament e gerenciar outros usuários.',
        ],

        'isUser' => [
            'label' => 'Usuário Filament?',
        ],

        'name' => [
            'label' => 'Nome',
        ],

        'password' => [

            'fieldset' => [

                'label' => [
                    'create' => 'Senha',
                    'edit' => 'Nova senha',
                ],

            ],

            'fields' => [

                'password' => [
                    'label' => 'Senha',
                ],

                'passwordConfirmation' => [
                    'label' => 'Confirme a senha',
                ],

            ],

        ],

        'roles' => [
            'label' => 'Funções',
            'placeholder' => 'Selecione uma função',
        ],

    ],

    'table' => [

        'columns' => [

            'email' => [
                'label' => 'E-mail',
            ],

            'name' => [
                'label' => 'Nome',
            ],

        ],

        'filters' => [

            'administrators' => [
                'label' => 'Administradores',
            ],

        ],

    ],

];
