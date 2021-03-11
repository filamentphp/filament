<?php

return [

    'form' => [

        'avatar' => [
            'label' => 'ავატარი',
        ],

        'email' => [
            'label' => 'ი-მეილი',
        ],

        'isAdmin' => [
            'label' => 'Filament ადმინისტრატორი',
            'helpMessage' => 'Filament ადმინისტრატორებს აქვთ ყველა გვერდზე წვდომა და შეუძლიათ სხვა მომხმარებლების მართვა',
        ],

        'isUser' => [
            'label' => 'Filament მომხმარებელი',
        ],

        'name' => [
            'label' => 'სახელი',
        ],

        'password' => [

            'fieldset' => [

                'label' => [
                    'create' => 'პაროლი',
                    'edit' => 'ახალი პაროლი',
                ],

            ],

            'fields' => [

                'password' => [
                    'label' => 'პაროლი',
                ],

                'passwordConfirmation' => [
                    'label' => 'გაიმეორეთ პაროლი',
                ],

            ],

        ],

        'roles' => [
            'label' => 'როლები',
            'placeholder' => 'აირჩიეთ როლი',
        ],

    ],

    'table' => [

        'columns' => [

            'email' => [
                'label' => 'ი-მეილი',
            ],

            'name' => [
                'label' => 'სახელი',
            ],

        ],

        'filters' => [

            'administrators' => [
                'label' => 'ადმინისტრატორები',
            ],

        ],

    ],

];
