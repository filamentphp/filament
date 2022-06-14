<?php

return [

    'title' => 'تعديل :label',

    'breadcrumb' => 'تعديل',

    'actions' => [

        'delete' => [

            'label' => 'حذف',

            'modal' => [

                'heading' => 'حذف :label',

                'subheading' => 'هل أنت متأكد من القيام بهذا؟',

                'buttons' => [

                    'delete' => [
                        'label' => 'حذف',
                    ],

                ],

            ],

            'messages' => [
                'deleted' => 'تم الحذف',
            ],

        ],

        'view' => [
            'label' => 'عرض',
        ],

    ],

    'form' => [

        'actions' => [

            'cancel' => [
                'label' => 'إلغاء',
            ],

            'save' => [
                'label' => 'حفظ',
            ],

        ],

    ],

    'messages' => [
        'saved' => 'تم الحفظ',
    ],

];
