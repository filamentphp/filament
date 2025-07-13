<?php

return [

    'title' => 'اکاؤنٹ بنائیں',

    'heading' => 'سائن اپ کریں',

    'actions' => [

        'login' => [
            'before' => 'یا',
            'label' => 'اپنے اکاؤنٹ میں سائن ان کریں',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'ای میل ایڈریس',
        ],

        'name' => [
            'label' => 'نام',
        ],

        'password' => [
            'label' => 'پاسورڈ',
            'validation_attribute' => 'پاسورڈ',
        ],

        'password_confirmation' => [
            'label' => 'پاسورڈ دوبارہ لکھیں',
        ],

        'actions' => [

            'register' => [
                'label' => 'اکاؤنٹ بنائیں',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'زیادہ بار رجسٹر کرنے کی کوشش کی گئی',
            'body' => 'براہ کرم :seconds سیکنڈ بعد دوبارہ کوشش کریں۔',
        ],

    ],

];
