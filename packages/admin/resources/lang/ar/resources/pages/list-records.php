<?php

return [

    'breadcrumb' => 'List',

    'actions' => [

        'create' => [

            'label' => 'New :label',

            'modal' => [

                'heading' => 'Create :label',

                'actions' => [

                    'create' => [
                        'label' => 'Create',
                    ],

                    'create_and_create_another' => [
                        'label' => 'Create & create another',
                    ],

                ],

            ],

            'messages' => [
                'created' => 'Created',
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
