<?php

return [

    'fields' => [

        'search_query' => [
            'label' => 'بحث',
            'placeholder' => 'بحث',
        ],

    ],

    'pagination' => [

        'label' => 'التنقل بين الصفحات',

        'overview' => 'عرض :first إلي :last من :total النتائج',

        'fields' => [

            'records_per_page' => [
                'label' => 'لكل صفحة',
            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'انتقل إلى صفحة :page',
            ],

            'next' => [
                'label' => 'التالي',
            ],

            'previous' => [
                'label' => 'السابق',
            ],

        ],

    ],

    'buttons' => [

        'filter' => [
            'label' => 'تصفية',
        ],

        'open_actions' => [
            'label' => 'فتح الإجراءات',
        ],

    ],

    'actions' => [

        'modal' => [

            'requires_confirmation_subheading' => 'هل إنت متأكد من القيام بهذا؟',

            'buttons' => [

                'cancel' => [
                    'label' => 'إلغاء',
                ],

                'confirm' => [
                    'label' => 'تأكيد',
                ],

                'submit' => [
                    'label' => 'إعتماد',
                ],

            ],

        ],

        'buttons' => [

            'select_all' => [
                'label' => 'تحديد كل السجلات :count',
            ],

        ],

    ],

    'empty' => [
        'heading' => 'لا توجد سجلات',
    ],

];
