<?php

return [

    'breadcrumb' => 'စာရင်း',

    'actions' => [

        'create' => [

            'label' => ':label အသစ်',

            'modal' => [

                'heading' => ':label ဖန်တီးပါ',

                'actions' => [

                    'create' => [
                        'label' => 'ဖန်တီးပါ',
                    ],

                    'create_and_create_another' => [
                        'label' => 'သိမ်းဆည်းပြီး နောက်တစ်ခုကို ဖန်တီးပါ',
                    ],

                ],

            ],

            'messages' => [
                'created' => 'သိမ်းဆည်းပြီး',
            ],

        ],

    ],

    'table' => [

        'actions' => [

            'delete' => [

                'label' => 'ဖျက်ပါ',

                'messages' => [
                    'deleted' => 'ဖျက်ပြီးပါပြီ',
                ],

            ],

            'edit' => [

                'label' => 'တည်းဖြတ်ပါ',

                'modal' => [

                    'heading' => ':label ကိုတည်းဖြတ်ပါ',

                    'actions' => [

                        'save' => [
                            'label' => 'မှတ်ပါ',
                        ],

                    ],

                ],

                'messages' => [
                    'saved' => 'သိမ်းဆည်းထားသည်',
                ],

            ],

            'view' => [
                'label' => 'စစ်ဆေးပါ',
            ],

        ],

        'bulk_actions' => [

            'delete' => [

                'label' => 'ရွေးထားသည့်အရာများကို ဖျက်ပါ',

                'messages' => [
                    'deleted' => 'ဖျက်ပါ',
                ],

            ],

        ],

    ],

];
