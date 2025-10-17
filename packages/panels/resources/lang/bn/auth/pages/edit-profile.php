<?php

return [

    'label' => 'প্রোফাইল',

    'form' => [

        'email' => [
            'label' => 'ইমেইল ঠিকানা',
        ],

        'name' => [
            'label' => 'নাম',
        ],

        'password' => [
            'label' => 'নতুন পাসওয়ার্ড',
            'validation_attribute' => 'পাসওয়ার্ড',
        ],

        'password_confirmation' => [
            'label' => 'নতুন পাসওয়ার্ড নিশ্চিত করুন',
            'validation_attribute' => 'পাসওয়ার্ড নিশ্চিতকরণ',
        ],

        'current_password' => [
            'label' => 'বর্তমান পাসওয়ার্ড',
            'below_content' => 'নিরাপত্তার জন্য, চালিয়ে যেতে আপনার পাসওয়ার্ড নিশ্চিত করুন।',
            'validation_attribute' => 'বর্তমান পাসওয়ার্ড',
        ],

        'actions' => [

            'save' => [
                'label' => 'পরিবর্তন সংরক্ষণ করুন',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'দুই-ফ্যাক্টর প্রমাণীকরণ (২এফএ)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'ইমেইল ঠিকানা পরিবর্তনের অনুরোধ পাঠানো হয়েছে',
            'body' => 'আপনার ইমেইল ঠিকানা পরিবর্তনের অনুরোধ :email এ পাঠানো হয়েছে। পরিবর্তন যাচাই করতে আপনার ইমেইল চেক করুন।',
        ],

        'saved' => [
            'title' => 'সংরক্ষিত হয়েছে',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'বাতিল করুন',
        ],

    ],

];
