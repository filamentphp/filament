<?php

return [

    'title' => 'Нэвтрэх',

    'heading' => 'Нэвтрэх',

    'actions' => [

        'register' => [
            'before' => 'эсвэл',
            'label' => 'бүртгүүлэх',
        ],

        'request_password_reset' => [
            'label' => 'Нууц үгээ мартсан уу?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Имэйл хаяг',
        ],

        'password' => [
            'label' => 'Нууц үг',
        ],

        'remember' => [
            'label' => 'Намайг сануул',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Нэвтрэх',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Нэвтрэх мэдээлэл буруу байна!',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Олон тооны нэвтрэх оролдлого',
            'body' => ':seconds секундын дараа дахин оролдоно уу!',
        ],

    ],

];
