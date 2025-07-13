<?php

return [

    'label' => 'پروفائل',

    'form' => [

        'email' => [
            'label' => 'ای میل ایڈریس',
        ],

        'name' => [
            'label' => 'نام',
        ],

        'password' => [
            'label' => 'نیا پاسورڈ',
            'validation_attribute' => 'پاسورڈ',
        ],

        'password_confirmation' => [
            'label' => 'نیا پاسورڈ دوبارہ لکھیں',
            'validation_attribute' => 'پاسورڈ کی تصدیق',
        ],

        'current_password' => [
            'label' => 'موجودہ پاسورڈ',
            'below_content' => 'سیکیورٹی کے لیے، جاری رکھنے سے پہلے پاسورڈ درج کریں۔',
            'validation_attribute' => 'موجودہ پاسورڈ',
        ],

        'actions' => [

            'save' => [
                'label' => 'تبدیلیاں محفوظ کریں',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'ٹو فیکٹر تصدیق (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'ای میل تبدیلی کی درخواست بھیج دی گئی',
            'body' => 'آپ کے :email پر ای میل تبدیلی کی تصدیق کے لیے ایک پیغام بھیجا گیا ہے۔',
        ],

        'saved' => [
            'title' => 'محفوظ ہو گیا',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'منسوخ کریں',
        ],

    ],

];
