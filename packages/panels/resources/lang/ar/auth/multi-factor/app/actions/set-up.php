<?php

return [

    'label' => 'إعداد',

    'modal' => [

        'heading' => 'إعداد تطبيق المصادقة',

        'description' => <<<'BLADE'
            ستحتاج إلى تطبيق مثل Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>، <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) لإكمال هذه العملية.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'امسح رمز الاستجابة السريعة هذا بتطبيق المصادقة:',

                'alt' => 'رمز الاستجابة السريعة للمسح بتطبيق المصادقة',

            ],

            'text_code' => [

                'instruction' => 'أو أدخل هذا الرمز يدوياً:',

                'messages' => [
                    'copied' => 'تم النسخ',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'يرجى حفظ رموز الاسترداد التالية في مكان آمن. سيتم عرضها مرة واحدة فقط، ولكنك ستحتاجها إذا فقدت الوصول إلى تطبيق المصادقة:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'أدخل الرمز المكون من 6 أرقام من تطبيق المصادقة',

                'validation_attribute' => 'الرمز',

                'below_content' => 'ستحتاج إلى إدخال الرمز المكون من 6 أرقام من تطبيق المصادقة في كل مرة تقوم فيها بتسجيل الدخول أو تنفيذ إجراءات حساسة.',

                'messages' => [

                    'invalid' => 'الرمز المُدخل غير صحيح.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'تفعيل تطبيق المصادقة',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'تم تفعيل تطبيق المصادقة',
        ],

    ],

];
