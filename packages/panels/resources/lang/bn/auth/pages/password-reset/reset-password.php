<?php

return [

    'title' => 'আপনার পাসওয়ার্ড রিসেট করুন',

    'heading' => 'আপনার পাসওয়ার্ড রিসেট করুন',

    'form' => [

        'email' => [
            'label' => 'ইমেইল ঠিকানা',
        ],

        'password' => [
            'label' => 'পাসওয়ার্ড',
            'validation_attribute' => 'পাসওয়ার্ড',
        ],

        'password_confirmation' => [
            'label' => 'পাসওয়ার্ড নিশ্চিত করুন',
        ],

        'actions' => [

            'reset' => [
                'label' => 'পাসওয়ার্ড রিসেট করুন',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'অতিরিক্ত রিসেট চেষ্টা',
            'body' => 'দয়া করে :seconds সেকেন্ড পর আবার চেষ্টা করুন।',
        ],

    ],

];
