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
                'title' => 'حذف شد',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'حذف انتخاب شده‌',

        'modal' => [

            'heading' => 'حذف :label انتخاب شده',

            'actions' => [

                'delete' => [
                    'label' => 'حذف انتخاب شده‌',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'حذف شد',
            ],
            'deleted_partial' => [
                'title' => ':count از :total حذف شد',
                'missing_authorization_failure_message' => 'شما اجازه حذف :count را ندارید.',
                'missing_processing_failure_message' => ':count نتوانست حذف شود.',
            ],

            'deleted_none' => [
                'title' => 'حذف انجام نشد',
                'missing_authorization_failure_message' => 'شما اجازه حذف :count را ندارید.',
                'missing_processing_failure_message' => ':count نتوانست حذف شود.',
            ],
        ],

    ],

];
