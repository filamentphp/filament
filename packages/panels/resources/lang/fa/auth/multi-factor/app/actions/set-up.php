<?php

return [

    'label' => 'راه‌اندازی',

    'modal' => [

        'heading' => 'راه‌اندازی اپلیکیشن تأییدکننده',

        'description' => <<<'BLADE'
            برای تکمیل این فرآیند نیاز به یک اپلیکیشن مانند Google Authenticator دارید (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>، <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>).
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'این کد QR را با اپلیکیشن تأییدکننده خود اسکن کنید:',

                'alt' => 'کد QR برای اسکن توسط اپلیکیشن تأییدکننده',

            ],

            'text_code' => [

                'instruction' => 'یا این کد را به‌صورت دستی وارد کنید:',

                'messages' => [
                    'copied' => 'کپی شد',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'لطفاً کدهای زیر را در جایی امن ذخیره کنید. این کدها فقط یک بار نمایش داده می‌شوند اما در صورت از دست دادن دسترسی به اپلیکیشن تأییدکننده به آن‌ها نیاز خواهید داشت:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'کد ۶ رقمی اپلیکیشن تأییدکننده را وارد کنید',

                'validation_attribute' => 'کد',

                'below_content' => 'شما باید هر بار هنگام ورود یا انجام عملیات حساس، کد ۶ رقمی اپلیکیشن تأییدکننده را وارد کنید.',

                'messages' => [

                    'invalid' => 'کدی که وارد کرده‌اید معتبر نیست.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'فعال‌سازی اپلیکیشن تأییدکننده',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'اپلیکیشن تأییدکننده فعال شد',
        ],

    ],

];
