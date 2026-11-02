<?php

return [

    'title' => 'چوونەژوورەوە',

    'heading' => 'چوونەژوورەوە',

    'actions' => [

        'register' => [
            'before' => 'یان',
            'label' => 'دروستکردنی هەژماری نوێ',
        ],

        'request_password_reset' => [
            'label' => 'تێپەڕەوشەت لەبیرکردووە؟',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'ئیمەیڵ',
        ],

        'password' => [
            'label' => 'تێپەڕەوشە',
        ],

        'remember' => [
            'label' => 'بەبیرمبهێنەوە',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'چوونەژوورەوە',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'ناسنامەکەت پشتڕاست بکەرەوە',

        'subheading' => 'بۆ بەردەوامبوون لە چوونەژوورەوە، پێویستە ناسنامەکەت پشتڕاست بکەیتەوە.',

        'form' => [

            'provider' => [
                'label' => 'دەتەوێت بە چ ڕێگەیەک پشتڕاستی بکەیتەوە؟',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'دڵنیاکردنەوەی چوونەژوورەوە',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'هیچ هەژمارێک بەو زانیارییانە تۆمارنەکراوە.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'زۆر هەوڵدراوە بۆ چوونەژوورەوە',
            'body' => 'تکایە دوای :seconds چرکە هەوڵ بدەرەوە.',
        ],

    ],

];
