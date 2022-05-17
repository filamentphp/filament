<?php

return [

    'breadcrumb' => 'القائمة',

    'actions' => [

        'create' => [

            'label' => 'إضافة :label',

            'modal' => [

                'heading' => 'إضافة :label',

                'actions' => [

                    'create' => [
                        'label' => 'إضافة',
                    ],

                    'create_and_create_another' => [
                        'label' => 'إضافة وبدء إضافة المزيد',
                    ],

                ],

            ],

            'messages' => [
                'created' => 'تمت الإضافة',
            ],

        ],

    ],

    'table' => [

        'actions' => [

            'delete' => [

                'label' => 'حذف',

                'modal' => [
                    'heading' => 'حذف :label',
                ],

                'messages' => [
                    'deleted' => 'تم الحذف',
                ],

            ],

            'edit' => [

                'label' => 'تعديل',

                'modal' => [

                    'heading' => 'تعديل :label',

                    'actions' => [

                        'save' => [
                            'label' => 'حفظ',
                        ],

                    ],

                ],

                'messages' => [
                    'saved' => 'تم الحفظ',
                ],

            ],

            'view' => [

                'label' => 'عرض',

                'modal' => [

                    'heading' => 'عرض :label',

                    'actions' => [

                        'close' => [
                            'label' => 'إغلاق',
                        ],

                    ],

                ],

            ],

        ],

        'bulk_actions' => [

            'delete' => [

                'label' => 'حذف المحدد',

                'messages' => [
                    'deleted' => 'تم الحذف',
                ],

            ],

        ],

    ],

];
