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

        'heading' => 'Verify your identity',

        'subheading' => 'To continue signing in, you need to verify your identity.',

        'form' => [

            'provider' => [
                'label' => 'How would you like to verify?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Confirm sign in',
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
