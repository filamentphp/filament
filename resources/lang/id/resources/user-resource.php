<?php

return [

    'form' => [

        'avatar' => [
            'label' => 'Avatar',
        ],

        'email' => [
            'label' => 'Alamat email',
        ],

        'isAdmin' => [
            'label' => 'Admin Filament?',
            'helpMessage' => 'Admin Filament dapat mengakses semua area yang dimiliki filament dan mengatur pengguna lain',
        ],

        'isUser' => [
            'label' => 'Pengguna Filament?',
        ],

        'name' => [
            'label' => 'Nama',
        ],

        'password' => [

            'fieldset' => [

                'label' => [
                    'create' => 'Password',
                    'edit' => 'Buat password baru',
                ],

            ],

            'fields' => [

                'password' => [
                    'label' => 'Password',
                ],

                'passwordConfirmation' => [
                    'label' => 'Konfirmasi password',
                ],

            ],

        ],

        'roles' => [
            'label' => 'Role',
            'placeholder' => 'Pilih role',
        ],

    ],

    'table' => [

        'columns' => [

            'email' => [
                'label' => 'Alamat email',
            ],

            'name' => [
                'label' => 'Nama',
            ],

        ],

        'filters' => [

            'administrators' => [
                'label' => 'Administrator',
            ],

        ],

    ],

];
