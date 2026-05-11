<?php

return [

    'single' => [

        'label' => 'মুছে ফেলুন',

        'modal' => [

            'heading' => ':label মুছে ফেলুন',

            'actions' => [

                'delete' => [
                    'label' => 'মুছে ফেলুন',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'মুছে ফেলা হয়েছে',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'নির্বাচিতগুলো মুছে ফেলুন',

        'modal' => [

            'heading' => 'নির্বাচিত :label মুছে ফেলুন',

            'actions' => [

                'delete' => [
                    'label' => 'মুছে ফেলুন',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'মুছে ফেলা হয়েছে',
            ],

            'deleted_partial' => [
                'title' => ':total এর মধ্যে :countটি মুছে ফেলা হয়েছে',
                'missing_authorization_failure_message' => 'আপনার :countটি মুছে ফেলার অনুমতি নেই।',
                'missing_processing_failure_message' => ':countটি মুছে ফেলা সম্ভব হয়নি।',
            ],

            'deleted_none' => [
                'title' => 'মুছে ফেলতে ব্যর্থ হয়েছে',
                'missing_authorization_failure_message' => 'আপনার :countটি মুছে ফেলার অনুমতি নেই।',
                'missing_processing_failure_message' => ':countটি মুছে ফেলা সম্ভব হয়নি।',
            ],

        ],

    ],

];
