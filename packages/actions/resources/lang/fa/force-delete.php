<?php

return [

    'single' => [

        'label' => 'حذف دائمی',

        'modal' => [

            'heading' => 'حذف دائمی :label',

            'actions' => [

                'delete' => [
                    'label' => 'حذف',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'رکورد حذف شد',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'حذف دائمی انتخاب شده',

        'modal' => [

            'heading' => 'حذف دائمی :label انتخاب شده',

            'actions' => [

                'delete' => [
                    'label' => 'حذف',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'رکوردها حذف شدند',
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
