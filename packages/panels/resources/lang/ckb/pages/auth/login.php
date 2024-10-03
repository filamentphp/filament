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
            'label' => 'وشەی نهێنیت لەبیرکردووە؟',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'ئیمەیڵ',
        ],

        'password' => [
            'label' => 'وشەی نهێنی',
        ],

        'remember' => [
            'label' => 'لەبیرم مەکە',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'چوونەژوورەوە',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'هیچ هەژمارێک بەو تۆمارە بوونی نییە.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'هەوڵی داواکاری چونەژورەوە زۆر نێردرا',
            'body' => 'تکایە هەوڵ بدەرەوە دوای :seconds چرکە.',
        ],

    ],

];
