<?php

return [

    'title' => 'Најава',

    'heading' => 'Најави се',

    'actions' => [

        'register' => [
            'before' => 'или',
            'label' => 'регистрирај сметка',
        ],

        'request_password_reset' => [
            'label' => 'Заборавена лозинка?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Е-пошта адреса',
        ],

        'password' => [
            'label' => 'Лозинка',
        ],

        'remember' => [
            'label' => 'Запомни ме',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Најави се',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'Потврди го твојот идентитет',

        'subheading' => 'За да продолжиш со најавувањето, треба да го потврдиш твојот идентитет.',

        'form' => [

            'provider' => [
                'label' => 'Како би сакал да потврдиш?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Потврди најава',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Овие податоци не се совпаѓаат со нашите записи.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Премногу обиди за најава',
            'body' => 'Ве молиме обидете се повторно за :seconds секунди.',
        ],

    ],

];
