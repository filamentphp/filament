<?php

return [

    'form' => [

        'avatar' => [
            'label' => 'Аватар',
        ],

        'email' => [
            'label' => 'Электронный адрес',
        ],

        'isAdmin' => [
            'label' => 'Администратор Filament?',
            'helpMessage' => 'Администраторы Filament имеют доступ ко всем страницам Filament и могут управлять другими пользователями.',
        ],

        'isUser' => [
            'label' => 'Пользователя Filament?',
        ],

        'name' => [
            'label' => 'Имя',
        ],

        'password' => [

            'fieldset' => [

                'label' => [
                    'create' => 'Пароль',
                    'edit' => 'Изменить пароль',
                ],

            ],

            'fields' => [

                'password' => [
                    'label' => 'Пароль',
                ],

                'passwordConfirmation' => [
                    'label' => 'Подтверждение пароля',
                ],

            ],

        ],

        'roles' => [
            'label' => 'Роли',
            'placeholder' => 'выберите Роль',
        ],

    ],

    'table' => [

        'columns' => [

            'email' => [
                'label' => 'Электронный адрес',
            ],

            'name' => [
                'label' => 'Имя',
            ],

        ],

        'filters' => [

            'administrators' => [
                'label' => 'Администраторы',
            ],

        ],

    ],

];
