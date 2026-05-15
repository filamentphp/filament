<?php

return [

    'title' => 'লগইন',

    'heading' => 'সাইন ইন করুন',

    'actions' => [

        'register' => [
            'before' => 'অথবা',
            'label' => 'অ্যাকাউন্টের জন্য সাইন আপ করুন',
        ],

        'request_password_reset' => [
            'label' => 'পাসওয়ার্ড ভুলে গেছেন?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'ইমেইল ঠিকানা',
        ],

        'password' => [
            'label' => 'পাসওয়ার্ড',
        ],

        'remember' => [
            'label' => 'মনে রাখুন',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'সাইন ইন',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'আপনার পরিচয় যাচাই করুন',

        'subheading' => 'সাইন ইন চালিয়ে যেতে আপনার পরিচয় যাচাই করা প্রয়োজন।',

        'form' => [

            'provider' => [
                'label' => 'আপনি কিভাবে যাচাই করতে চান?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'সাইন ইন নিশ্চিত করুন',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'এই তথ্যগুলো আমাদের রেকর্ডের সাথে মিলছে না।',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'অত্যধিক লগইন চেষ্টা',
            'body' => 'অনুগ্রহ করে :seconds সেকেন্ড পর আবার চেষ্টা করুন।',
        ],

    ],

];
