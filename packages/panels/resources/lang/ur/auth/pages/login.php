<?php

return [

    'title' => 'لاگ ان',

    'heading' => 'اکاؤنٹ میں سائن ان کریں',

    'actions' => [

        'register' => [
            'before' => 'یا',
            'label' => 'نیا اکاؤنٹ بنائیں',
        ],

        'request_password_reset' => [
            'label' => 'پاسورڈ بھول گئے ہیں؟',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'ای میل ایڈریس',
        ],

        'password' => [
            'label' => 'پاسورڈ',
        ],

        'remember' => [
            'label' => 'مجھے یاد رکھیں',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'سائن ان کریں',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'اپنی شناخت کی تصدیق کریں',

        'subheading' => 'جاری رکھنے کے لیے، آپ کو اپنی شناخت کی تصدیق کرنا ہوگی۔',

        'form' => [

            'provider' => [
                'label' => 'تصدیق کرنے کا طریقہ منتخب کریں',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'سائن ان کی تصدیق کریں',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'یہ معلومات ہمارے ریکارڈ سے مطابقت نہیں رکھتیں۔',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'زیادہ لاگ ان کوششیں',
            'body' => 'براہ کرم :seconds سیکنڈ بعد دوبارہ کوشش کریں۔',
        ],

    ],

];
