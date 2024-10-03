<?php

return [

    'title' => 'Възстановяване на парола',

    'heading' => 'Забравена парола?',

    'actions' => [

        'login' => [
            'label' => 'Влезте в своя акаунт',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Имейл',
        ],

        'actions' => [

            'request' => [
                'label' => 'Изпратете ми линк за възстановяване на паролата',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Твърде много опити за възстановяване на парола',
            'body' => 'Моля, опитайте отново след :seconds секунди.',
        ],

    ],

];
