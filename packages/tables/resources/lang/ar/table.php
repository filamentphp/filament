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

        'overview' => 'عرض :first إلى :last من :total النتائج',

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

            'requires_confirmation_subheading' => 'هل أنت متأكد من القيام بهذا؟',

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

    ],

    'empty' => [
        'heading' => 'لا توجد سجلات',
    ],

    'filters' => [

        'buttons' => [

            'reset' => [
                'label' => 'إعادة ضبط الفلترة',
            ],

            'close' => [
                'label' => 'غلق',
            ],

        ],

        'multi_select' => [
            'placeholder' => 'الكل',
        ],

        'select' => [
            'placeholder' => 'الكل',
        ],

    ],

    'selection_indicator' => [

        'selected_count' => '{1} تم تحديد سجل واحد.|[2,*] :count سجل/سجلات تم تحديدها.',

        'buttons' => [

            'select_all' => [
                'label' => 'تحديد كل السجلات :count',
            ],

            'deselect_all' => [
                'label' => 'إلغاء تحديد الكل',
            ],

        ],

    ],

];
