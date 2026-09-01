<?php

return [

    'title' => 'Нууц үг шинэчлэх',

    'heading' => 'Нууц үг шинэчлэх',

    'form' => [

        'email' => [
            'label' => 'Имэйл хаяг',
        ],

        'password' => [
            'label' => 'Нууц үг',
            'validation_attribute' => 'нууц үг',
        ],

        'password_confirmation' => [
            'label' => 'Нууц үгийг давтах',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Нууц үгийг шинэчлэх',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Олон тооны шинэчлэх оролдлого',
            'body' => ':seconds секундын дараа дахин оролдоно уу!',
        ],

    ],

];
