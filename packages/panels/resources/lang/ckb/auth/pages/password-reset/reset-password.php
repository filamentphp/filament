<?php

return [

    'title' => 'نوێکردنەوەی تێپەڕەوشە',

    'heading' => 'نوێکردنەوەی تێپەڕەوشە',

    'form' => [

        'email' => [
            'label' => 'ئیمەیڵ',
        ],

        'password' => [
            'label' => 'تێپەڕەوشە',
            'validation_attribute' => 'تێپەڕەوشە',
        ],

        'password_confirmation' => [
            'label' => 'دڵنیاکردنەوەی تێپەڕەوشە',
        ],

        'actions' => [

            'reset' => [
                'label' => 'رێکخستنەوەی تێپەڕەوشە',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'زۆر هەوڵی ڕێکخستنەوەی تێپەڕەوشە درا',
            'body' => 'تکایە دوای :seconds چرکە هەوڵ بدەرەوە.',
        ],

    ],

];
