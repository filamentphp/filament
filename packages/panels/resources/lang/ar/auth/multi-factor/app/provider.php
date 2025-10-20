<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'تطبيق المصادقة الثنائية',

            'below_content' => 'استخدم تطبيقًا آمنًا لتوليد رمز مؤقت للتحقق من تسجيل الدخول.',

            'messages' => [
                'enabled' => 'مفعل',
                'disabled' => 'معطل',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'استخدم رمزًا من تطبيق المصادقة',

        'code' => [

            'label' => 'أدخل الرمز المكون من 6 أرقام من تطبيق المصادقة',

            'validation_attribute' => 'الرمز',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'استخدم رمز الاسترداد بدلاً من ذلك',
                ],

            ],

            'messages' => [

                'invalid' => 'الرمز الذي أدخلته غير صالح.',

            ],

        ],

        'recovery_code' => [

            'label' => 'أو، أدخل رمز الاسترداد',

            'validation_attribute' => 'رمز الاسترداد',

            'messages' => [

                'invalid' => 'رمز الاسترداد الذي أدخلته غير صالح.',

            ],

        ],

    ],

];
