<?php

return [
    'title' => 'I-reset ang password mo',
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
                'label' => 'Ipadala ang email',
            ],
        ],
    ],
    'notifications' => [
        'sent' => [
            'body' => 'Kung hindi umiiral ang account mo, hindi mo matatanggap ang email.',
        ],
        'throttled' => [
            'title' => 'Masyadong maraming request',
            'body' => 'Subukan ulit pagkalipas ng :seconds segundo.',
        ],
    ],
];
