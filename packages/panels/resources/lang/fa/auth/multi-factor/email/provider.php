<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'کدهای تأیید ایمیل',

            'below_content' => 'یک کد موقت به آدرس ایمیل شما ارسال می‌شود تا هنگام ورود هویت شما تأیید شود.',

            'messages' => [
                'enabled' => 'فعال',
                'disabled' => 'غیرفعال',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'ارسال کد به ایمیل شما',

        'code' => [

            'label' => 'کد ۶ رقمی ارسال‌شده به ایمیل خود را وارد کنید',

            'validation_attribute' => 'کد',

            'actions' => [

                'resend' => [

                    'label' => 'ارسال کد جدید به ایمیل',

                    'notifications' => [

                        'resent' => [
                            'title' => 'کد جدیدی به ایمیل شما ارسال شد',
                        ],

                    ],

                ],

            ],

            'messages' => [

                'invalid' => 'کدی که وارد کرده‌اید معتبر نیست.',

            ],

        ],

    ],

];
