<?php

return [

    'title' => 'Регистрирај се',

    'heading' => 'Регистрирај се',

    'actions' => [

        'login' => [
            'before' => 'или',
            'label' => 'најави се на твојата сметка',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Е-пошта адреса',
        ],

        'name' => [
            'label' => 'Име',
        ],

        'password' => [
            'label' => 'Лозинка',
            'validation_attribute' => 'лозинка',
        ],

        'password_confirmation' => [
            'label' => 'Потврди лозинка',
        ],

        'actions' => [

            'register' => [
                'label' => 'Регистрирај се',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Премногу обиди за регистрација',
            'body' => 'Ве молиме обидете се повторно за :seconds секунди.',
        ],

    ],

];
