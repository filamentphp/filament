<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'កម្មវិធីផ្ទៀងផ្ទាត់ (Authenticator app)',

            'below_content' => 'ប្រើកម្មវិធីដែលមានសុវត្ថិភាពដើម្បីបង្កើតកូដបណ្តោះអាសន្នសម្រាប់ការផ្ទៀងផ្ទាត់ការចូល។',

            'messages' => [
                'enabled' => 'បានបើក',
                'disabled' => 'បានបិទ',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'ប្រើកូដពីកម្មវិធីផ្ទៀងផ្ទាត់របស់អ្នក',

        'code' => [

            'label' => 'បញ្ចូលកូដ 6 ខ្ទង់ពីកម្មវិធីផ្ទៀងផ្ទាត់',

            'validation_attribute' => 'កូដ',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'ប្រើកូដសង្គ្រោះជំនួសវិញ',
                ],

            ],

            'messages' => [
                'invalid' => 'កូដដែលអ្នកបានបញ្ចូលមិនត្រឹមត្រូវទេ។',
            ],

        ],

        'recovery_code' => [

            'label' => 'ឬ បញ្ចូលកូដសង្គ្រោះ',

            'validation_attribute' => 'កូដសង្គ្រោះ',

            'messages' => [
                'invalid' => 'កូដសង្គ្រោះដែលអ្នកបានបញ្ចូលមិនត្រឹមត្រូវទេ។',
            ],

        ],

    ],

];
