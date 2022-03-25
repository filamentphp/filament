<?php

return [

    'breadcrumb' => 'لیست',

    'actions' => [

        'create' => [

            'label' => ':label جدید',

            'modal' => [

                'heading' => ':label ساختن',

                'actions' => [

                    'create' => [
                        'label' => 'ساختن',
                    ],

                    'create_and_create_another' => [
                        'label' => 'ساختن و ساختن یکی دیگر',
                    ],

                ],

            ],

            'messages' => [
                'created' => 'ساخته شد',
            ],
        ],

    ],

    'table' => [

        'actions' => [

            'delete' => [

                'label' => 'حذف',

                'messages' => [
                    'deleted' => 'حذف شد',
                ],

            ],

            'edit' => [

                'label' => 'اصلاح',

                'modal' => [

                    'heading' => 'اصلاح :label',

                    'actions' => [

                        'save' => [
                            'label' => 'ذخیره',
                        ],

                    ],

                ],

                'messages' => [
                    'saved' => 'ذخیره شد',
                ],

            ],

            'view' => [
                'label' => 'مشاهده',
            ],

        ],

        'bulk_actions' => [

            'delete' => [

                'label' => 'حذف انتخاب شده',

                'messages' => [
                    'deleted' => 'حذف شد',
                ],

            ],

        ],

    ],

];
