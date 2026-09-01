<?php

return [

    'title' => 'নিবন্ধন',

    'heading' => 'সাইন আপ করুন',

    'actions' => [

        'login' => [
            'before' => 'অথবা',
            'label' => 'আপনার অ্যাকাউন্টে সাইন ইন করুন',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'ইমেইল ঠিকানা',
        ],

        'name' => [
            'label' => 'নাম',
        ],

        'password' => [
            'label' => 'পাসওয়ার্ড',
            'validation_attribute' => 'পাসওয়ার্ড',
        ],

        'password_confirmation' => [
            'label' => 'পাসওয়ার্ড নিশ্চিত করুন',
        ],

        'actions' => [

            'register' => [
                'label' => 'সাইন আপ',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'অতিরিক্ত নিবন্ধন চেষ্টা',
            'body' => 'দয়া করে :seconds সেকেন্ড পর আবার চেষ্টা করুন।',
        ],

    ],

];
