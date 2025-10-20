<?php

return [

    'single' => [

        'label' => 'حذف',

        'modal' => [

            'heading' => 'حذف :label',

            'actions' => [

                'delete' => [
                    'label' => 'حذف',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'تم الحذف',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'حذف المحدد',

        'modal' => [

            'heading' => 'حذف المحدد :label',

            'actions' => [

                'delete' => [
                    'label' => 'حذف',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'تم الحذف',
            ],

            'deleted_partial' => [
                'title' => 'تم حذف :count من :total',
                'missing_authorization_failure_message' => 'ليس لديك إذن لحذف :count.',
                'missing_processing_failure_message' => ':count لم يتم حذفه.',
            ],

            'deleted_none' => [
                'title' => 'لم يتم حذف أي شيء',
                'missing_authorization_failure_message' => 'ليس لديك إذن لحذف :count.',
                'missing_processing_failure_message' => 'لم يتم حذف :count.',
            ],

        ],

    ],

];
