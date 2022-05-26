<?php

return [

    'title' => 'اصلاح :label',

    'breadcrumb' => 'اصلاح',

    'actions' => [

        'delete' => [

            'label' => 'حذف',

            'modal' => [

                'heading' => 'حذف :label',

                'subheading' => 'آیا برای انجام اینکار مطمئن هستید؟',

                'buttons' => [

                    'delete' => [
                        'label' => 'حذف',
                    ],

                ],

            ],

            'messages' => [
                'deleted' => 'حذف شد',
            ],

        ],

        'view' => [
            'label' => 'مشاهده',
        ],

    ],

    'form' => [

        'actions' => [

            'cancel' => [
                'label' => 'لغو',
            ],

            'save' => [
                'label' => 'ذخیره',
            ],

        ],

    ],

    'messages' => [
        'saved' => 'ذخیره شد',
    ],

];
