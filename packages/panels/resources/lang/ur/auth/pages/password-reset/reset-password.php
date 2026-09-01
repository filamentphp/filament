<?php

return [

    'title' => 'اپنا پاسورڈ ری سیٹ کریں',

    'heading' => 'پاسورڈ تبدیل کریں',

    'form' => [

        'email' => [
            'label' => 'ای میل ایڈریس',
        ],

        'password' => [
            'label' => 'نیا پاسورڈ',
            'validation_attribute' => 'پاسورڈ',
        ],

        'password_confirmation' => [
            'label' => 'پاسورڈ دوبارہ لکھیں',
        ],

        'actions' => [

            'reset' => [
                'label' => 'پاسورڈ ری سیٹ کریں',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'کافی بار کوشش ہو چکی ہے',
            'body' => 'براہ کرم :seconds سیکنڈ بعد دوبارہ کوشش کریں۔',
        ],

    ],

];
