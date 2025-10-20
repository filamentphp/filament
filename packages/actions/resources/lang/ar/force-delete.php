<?php

return [

    'single' => [

        'label' => 'حذف نهائي',

        'modal' => [

            'heading' => 'حذف نهائي لـ :label',

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

        'label' => 'حذف المحدد نهائيا',

        'modal' => [

            'heading' => 'حذف نهائي لـ :label',

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
                'title' => 'تم حذف :count من أصل :total',
                'missing_authorization_failure_message' => 'ليس لديك صلاحية لحذف :count.',
                'missing_processing_failure_message' => 'تعذر حذف :count.',
            ],

            'deleted_none' => [
                'title' => 'فشل في الحذف',
                'missing_authorization_failure_message' => 'ليس لديك صلاحية لحذف :count.',
                'missing_processing_failure_message' => 'تعذر حذف :count.',
            ],

        ],

    ],

];
