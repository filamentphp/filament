<?php

return [

    'title' => 'Бүртгүүлэх',

    'heading' => 'Бүртгүүлэх',

    'actions' => [

        'login' => [
            'before' => 'эсвэл',
            'label' => 'өөрийн бүртгэлээр нэвтрэх',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Имэйл хаяг',
        ],

        'name' => [
            'label' => 'Нэр',
        ],

        'password' => [
            'label' => 'Нууц үг',
            'validation_attribute' => 'нууц үг',
        ],

        'password_confirmation' => [
            'label' => 'Нууц үгийг давтан оруулах',
        ],

        'actions' => [

            'register' => [
                'label' => 'Бүртгүүлэх',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Олон тооны бүртгүүлэх оролдого',
            'body' => ':seconds секундын дараа дахин оролдоно уу.',
        ],

    ],

];
