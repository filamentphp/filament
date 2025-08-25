<?php

return [

    'column_manager' => [

        'heading' => 'الأعمدة',

        'actions' => [

            'apply' => [
                'label' => 'تطبيق الأعمدة',
            ],

            'reset' => [
                'label' => 'إعادة ضبط التصفيات',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'إجراء | إجراءات',
        ],

        'select' => [

            'loading_message' => 'جاري التحميل...',

            'no_search_results_message' => 'لا توجد خيارات مطابقة لبحثك.',

            'placeholder' => 'اختر',

            'searching_message' => 'جاري البحث...',

            'search_prompt' => 'ابدأ الكتابة للبحث...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'عرض :count أقل',
                'expand_list' => 'عرض :count أكثر',
            ],

            'more_list_items' => 'و :count إضافية',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'تحديد/إلغاء تحديد كافة العناصر للإجراءات الجماعية.',
        ],

        'bulk_select_record' => [
            'label' => 'تحديد/إلغاء تحديد العنصر :key للإجراءات الجماعية.',
        ],

        'bulk_select_group' => [
            'label' => 'تحديد/إلغاء تحديد المجموعة :title للإجراءات الجماعية.',
        ],

        'search' => [
            'label' => 'بحث',
            'placeholder' => 'بحث',
            'indicator' => 'بحث',
        ],

    ],

    'summary' => [

        'heading' => 'الملخص',

        'subheadings' => [
            'all' => 'كافة :label',
            'group' => 'ملخص :group',
            'page' => 'هذه الصفحة',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'المتوسط',
            ],

            'count' => [
                'label' => 'العدد',
            ],

            'sum' => [
                'label' => 'المجموع',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'إنهاء إعادة ترتيب السجلات',
        ],

        'enable_reordering' => [
            'label' => 'إعادة ترتيب السجلات',
        ],

        'filter' => [
            'label' => 'تصفية',
        ],

        'group' => [
            'label' => 'مجموعة',
        ],

        'open_bulk_actions' => [
            'label' => 'الإجراءات',
        ],

        'column_manager' => [
            'label' => 'تبديل الأعمدة',
        ],

    ],

    'empty' => [

        'heading' => 'لا توجد :model',

        'description' => 'قم بإضافة :model للبدء.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'تطبيق التصفيات',
            ],

            'remove' => [
                'label' => 'إلغاء التصفيات',
            ],

            'remove_all' => [
                'label' => 'إلغاء كافة التصفيات',
                'tooltip' => 'إلغاء كافة التصفيات',
            ],

            'reset' => [
                'label' => 'إعادة ضبط التصفيات',
            ],

        ],

        'heading' => 'التصفيات',

        'indicator' => 'التصفيات النشطة',

        'multi_select' => [
            'placeholder' => 'الكل',
        ],

        'select' => [

            'placeholder' => 'الكل',

            'relationship' => [
                'empty_option_label' => 'لا يوجد اختيار',
            ],

        ],

        'trashed' => [

            'label' => 'السجلات المحذوفة',

            'only_trashed' => 'السجلات المحذوفة فقط',

            'with_trashed' => 'مع السجلات المحذوفة',

            'without_trashed' => 'بدون السجلات المحذوفة',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'تجميع حسب',
            ],

            'direction' => [

                'label' => 'إتجاه التجميع',

                'options' => [
                    'asc' => 'تصاعدي',
                    'desc' => 'تنازلي',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'قم بسحب وإسقاط السجلات بالترتيب.',

    'selection_indicator' => [

        'selected_count' => '{1} تم تحديد سجل واحد|[3,10] تم تحديد :count سجلات |[2,*] تم تحديد :count سجل',

        'actions' => [

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

    'default_model_label' => 'سجل',

];
