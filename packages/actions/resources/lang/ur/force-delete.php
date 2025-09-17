<?php

return [

    'single' => [

        'label' => 'زبردستی حذف کریں',

        'modal' => [

            'heading' => ':label کو زبردستی حذف کریں',

            'actions' => [

                'delete' => [
                    'label' => 'حذف کریں',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'حذف کر دیا گیا',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'منتخب شدہ زبردستی حذف کریں',

        'modal' => [

            'heading' => 'منتخب شدہ :label کو زبردستی حذف کریں',

            'actions' => [

                'delete' => [
                    'label' => 'حذف کریں',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'حذف کر دیے گئے',
            ],

            'deleted_partial' => [
                'title' => ':total میں سے :count حذف کیے گئے',
                'missing_authorization_failure_message' => 'آپ کو :count حذف کرنے کی اجازت نہیں ہے۔',
                'missing_processing_failure_message' => ':count کو حذف نہیں کیا جا سکا۔',
            ],

            'deleted_none' => [
                'title' => 'حذف ناکام رہا',
                'missing_authorization_failure_message' => 'آپ کو :count حذف کرنے کی اجازت نہیں ہے۔',
                'missing_processing_failure_message' => ':count کو حذف نہیں کیا جا سکا۔',
            ],

        ],

    ],

];
