<?php

return [

    'title' => 'دروسکردنی هەژمار',

    'heading' => 'دروسکردنی هەژمار',

    'actions' => [

        'login' => [
            'before' => 'یان',
            'label' => 'چوونەژوورەوە',
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
            'label' => 'وشەی نهێنی',
            'validation_attribute' => 'وشەی نهێنی',
        ],

        'password_confirmation' => [
            'label' => 'دڵنیابوونەوەی وشەی نهێنی',
        ],

        'actions' => [

            'register' => [
                'label' => 'دروسکردنی هەژمار',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'هەوڵی دروسکردنی هەژمار زۆر نێردرا',
            'body' => 'تکایە هەوڵ بدەرەوە دوای :seconds چرکە.',
        ],

    ],

];
