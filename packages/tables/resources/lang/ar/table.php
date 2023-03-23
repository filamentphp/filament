<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'و :count أكثر',
        ],

        'messages' => [
            'copied' => 'تم النسخ',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'تحديد / إلغاء تحديد كافة العناصر للإجراءات الجماعية.',
        ],

        'bulk_select_record' => [
            'label' => 'تحديد / إلغاء تحديد العنصر :key للإجراءات الجماعية',
        ],

        'search_query' => [
            'label' => 'بحث',
            'placeholder' => 'بحث',
        ],

    ],

    'pagination' => [

        'label' => 'التنقل بين الصفحات',

        'overview' => '{1} عرض نتيجة واحدة|[3,10] عرض :first إلي :last من :total نتائج|[2,*] عرض :first إلي :last من :total نتيجة',

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

        'buttons' => [

            'reset_column_searches' => [
                'label' => 'مسح البحث في العمود',
            ],

        ],

    ],

    'filters' => [

        'buttons' => [

            'remove' => [
                'label' => 'إلغاء الفلاتر',
            ],

            'remove_all' => [
                'label' => 'إلغاء كافة الفلاتر',
                'tooltip' => 'إلغاء كافة الفلاتر',
            ],

            'reset' => [
                'label' => 'إعادة ضبط الفلاتر',
            ],

        ],

        'indicator' => 'الفلاتر النشطة',

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

        'selected_count' => '{1} تم تحديد سجل واحد|[3,10] تم تحديد :count سجلات |[2,*] تم تحديد :count سجل',

        'buttons' => [

            'select_all' => [
                'label' => 'تحديد كل السجلات :count',
            ],

            'deselect_all' => [
                'label' => 'إلغاء تحديد الكل',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'ترتيب حسب',
            ],

            'direction' => [

                'label' => 'اتجاه الترتيب',

                'options' => [
                    'asc' => 'تصاعدي',
                    'desc' => 'تنازلي',
                ],

            ],

        ],

    ],

];
