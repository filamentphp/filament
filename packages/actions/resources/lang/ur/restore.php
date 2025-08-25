<?php

return [

    'single' => [

        'label' => 'بحال کریں',

        'modal' => [

            'heading' => ':label کو بحال کریں',

            'actions' => [

                'restore' => [
                    'label' => 'بحال کریں',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'بحال کر دیا گیا',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'منتخب شدہ کو بحال کریں',

        'modal' => [

            'heading' => 'منتخب شدہ :label کو بحال کریں',

            'actions' => [

                'restore' => [
                    'label' => 'بحال کریں',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'بحال کر دیے گئے',
            ],

            'restored_partial' => [
                'title' => ':total میں سے :count بحال کیے گئے',
                'missing_authorization_failure_message' => 'آپ کو :count بحال کرنے کی اجازت نہیں ہے۔',
                'missing_processing_failure_message' => ':count کو بحال نہیں کیا جا سکا۔',
            ],

            'restored_none' => [
                'title' => 'بحال کرنے میں ناکامی',
                'missing_authorization_failure_message' => 'آپ کو :count بحال کرنے کی اجازت نہیں ہے۔',
                'missing_processing_failure_message' => ':count کو بحال نہیں کیا جا سکا۔',
            ],

        ],

    ],

];
