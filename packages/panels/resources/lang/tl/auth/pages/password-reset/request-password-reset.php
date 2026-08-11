<?php

return [

    'title' => 'I-reset ang iyong password',

    'heading' => 'Nakalimutan ang password?',

    'actions' => [

        'login' => [
            'label' => 'bumalik sa login',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Address ng email',
        ],

        'actions' => [

            'request' => [
                'label' => 'Magpadala ng email',
            ],

        ],

    ],

    'notifications' => [

        'sent' => [
            'body' => 'Kung wala ang account mo, hindi mo matatanggap ang email.',
        ],

        'throttled' => [
            'title' => 'Masyadong maraming request',
            'body' => 'Subukan ulit pagkalipas ng :seconds segundo.',
        ],

    ],

];
