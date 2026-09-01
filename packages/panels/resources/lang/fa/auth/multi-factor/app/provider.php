<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'اپلیکیشن تأییدکننده',

            'below_content' => 'از یک اپلیکیشن امن برای تولید کد موقت ورود استفاده کنید.',

            'messages' => [
                'enabled' => 'فعال',
                'disabled' => 'غیرفعال',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'استفاده از کد اپلیکیشن تأییدکننده',

        'code' => [

            'label' => 'کد ۶ رقمی اپلیکیشن تأییدکننده را وارد کنید',

            'validation_attribute' => 'کد',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'به‌جای آن از کد بازیابی استفاده کنید',
                ],

            ],

            'messages' => [

                'invalid' => 'کدی که وارد کرده‌اید معتبر نیست.',

            ],

        ],

        'recovery_code' => [

            'label' => 'یا کد بازیابی را وارد کنید',

            'validation_attribute' => 'کد بازیابی',

            'messages' => [

                'invalid' => 'کد بازیابی واردشده معتبر نیست.',

            ],

        ],

    ],

];
