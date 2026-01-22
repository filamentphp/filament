<?php

return [

    'title' => 'Ресетирај ја твојата лозинка',

    'heading' => 'Ресетирај ја твојата лозинка',

    'form' => [

        'email' => [
            'label' => 'Е-пошта адреса',
        ],

        'password' => [
            'label' => 'Лозинка',
            'validation_attribute' => 'лозинка',
        ],

        'password_confirmation' => [
            'label' => 'Потврди лозинка',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Ресетирај лозинка',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Премногу обиди за ресетирање',
            'body' => 'Ве молиме обидете се повторно за :seconds секунди.',
        ],

    ],

];
