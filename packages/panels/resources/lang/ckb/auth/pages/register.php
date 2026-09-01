<?php

return [

    'title' => 'دروستکردنی هەژمار',

    'heading' => 'دروستکردنی هەژمار',

    'actions' => [

        'login' => [
            'before' => 'یان',
            'label' => 'چوونەژوورەوە بۆ هەژمارەکەت',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'ئیمەیڵ',
        ],

        'name' => [
            'label' => 'ناو',
        ],

        'password' => [
            'label' => 'تێپەڕەوشە',
            'validation_attribute' => 'تێپەڕەوشە',
        ],

        'password_confirmation' => [
            'label' => 'دڵنیاکردنەوەی تێپەڕەوشە',
        ],

        'actions' => [

            'register' => [
                'label' => 'دروستکردنی هەژمار',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'زۆر هەوڵدرا بۆ دروستکردنی هەژمار',
            'body' => 'تکایە دوای :seconds چرکە هەوڵ بدەرەوە.',
        ],

    ],

];
