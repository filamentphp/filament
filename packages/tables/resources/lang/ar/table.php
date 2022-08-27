<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'و :count أكثر',
        ],

    ],

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

                'options' => [
                    'all' => 'الكل',
                ],

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

        'disable_reordering' => [
            'label' => 'إنهاء إعادة ترتيب السجلات',
        ],

        'enable_reordering' => [
            'label' => 'إعادة ترتيب السجلات',
        ],

        'filter' => [
            'label' => 'تصفية',
        ],

        'open_actions' => [
            'label' => 'فتح الإجراءات',
        ],

        'toggle_columns' => [
            'label' => 'تبديل الأعمدة',
        ],

    ],

    'empty' => [
        'heading' => 'لا توجد سجلات',
    ],

    'filters' => [

        'buttons' => [

            'reset' => [
                'label' => 'إعادة ضبط المصفيات',
                'tooltip' => 'إعادة ضبط المصفيات',
            ],

        ],

        'indicator' => 'المصفيات النشطة',

        'multi_select' => [
            'placeholder' => 'الكل',
        ],

        'select' => [
            'placeholder' => 'الكل',
        ],

        'trashed' => [

            'label' => 'السجلات المحذوفة',

            'only_trashed' => 'السجلات المحذوفة فقط',

            'with_trashed' => 'مع السجلات المحذوفة',

            'without_trashed' => 'بدون السجلات المحذوفة',

        ],

    ],

    'reorder_indicator' => 'قم بسحب وإسقاط السجلات بالترتيب.',

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
