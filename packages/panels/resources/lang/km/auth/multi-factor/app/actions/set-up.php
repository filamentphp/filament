<?php

return [

    'label' => 'រៀបចំ',

    'modal' => [

        'heading' => 'រៀបចំកម្មវិធីផ្ទៀងផ្ទាត់ (Authenticator app)',

        'description' => 'អ្នកនឹងត្រូវការកម្មវិធីដូចជា Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) ដើម្បីបញ្ចប់ដំណើរការនេះ។',

        'content' => [

            'qr_code' => [
                'instruction' => 'ស្កែនកូដ QR នេះជាមួយកម្មវិធីផ្ទៀងផ្ទាត់របស់អ្នក៖',
                'alt' => 'កូដ QR សម្រាប់ស្កែនជាមួយកម្មវិធីផ្ទៀងផ្ទាត់',
            ],

            'text_code' => [
                'instruction' => 'ឬ បញ្ចូលកូដនេះដោយដៃ៖',
                'messages' => [
                    'copied' => 'បានចម្លង',
                ],
            ],

            'recovery_codes' => [
                'instruction' => 'សូមរក្សាទុកកូដសង្គ្រោះដូចខាងក្រោមនៅកន្លែងដែលមានសុវត្ថិភាព។ ពួកវានឹងត្រូវបានបង្ហាញតែម្តងគត់ ប៉ុន្តែអ្នកនឹងត្រូវការវាប្រសិនបើអ្នកបាត់បង់ការចូលប្រើកម្មវិធីផ្ទៀងផ្ទាត់របស់អ្នក៖',
            ],

        ],

        'form' => [

            'code' => [

                'label' => 'បញ្ចូលកូដ 6 ខ្ទង់ពីកម្មវិធីផ្ទៀងផ្ទាត់',

                'validation_attribute' => 'កូដ',

                'below_content' => 'អ្នកនឹងត្រូវបញ្ចូលកូដ 6 ខ្ទង់ពីកម្មវិធីផ្ទៀងផ្ទាត់របស់អ្នករាល់ពេលដែលអ្នកចូល ឬអនុវត្តសកម្មភាពរសើប។',

                'messages' => [
                    'invalid' => 'កូដដែលអ្នកបានបញ្ចូលមិនត្រឹមត្រូវទេ។',
                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'បើកកម្មវិធីផ្ទៀងផ្ទាត់',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'កម្មវិធីផ្ទៀងផ្ទាត់ត្រូវបានបើក',
        ],

    ],

];
