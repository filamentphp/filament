<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'و :count تا بیشتر',
        ],

        'messages' => [
            'copied' => 'کپی شد',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'انتخاب/عدم انتخاب تمامی موارد برای اقدامات گروهی',
        ],

        'bulk_select_record' => [
            'label' => 'انتخاب/عدم انتخاب مورد :key برای اقدامات گروهی',
        ],

        'search_query' => [
            'label' => 'جستجو',
            'placeholder' => 'جستجو',
        ],

    ],

    'pagination' => [

        'label' => 'صفحه بندی',

        'overview' => 'در حال نمایش :first به :last از :total نتایج',

        'fields' => [

            'records_per_page' => [

                'label' => 'در هر صفحه',

                'options' => [
                    'all' => 'همه',
                ],

            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'برو به صفحه :page',
            ],

            'next' => [
                'label' => 'بعدی',
            ],

            'previous' => [
                'label' => 'قبلی',
            ],

        ],

    ],

    'buttons' => [

        'disable_reordering' => [
            'label' => 'اتمام بازچینش رکوردها',
        ],

        'enable_reordering' => [
            'label' => 'بازچینش رکوردها',
        ],

        'filter' => [
            'label' => 'فیلتر',
        ],

        'open_actions' => [
            'label' => 'بازکردن عملیات',
        ],

        'toggle_columns' => [
            'label' => 'باز/بستن ستون‌ها',
        ],

    ],

    'empty' => [
        'heading' => 'هیچ رکوردی یافت نشد',

        'buttons' => [

            'reset_column_searches' => [
                'label' => 'جستجوی ستونی را پاک کن',
            ],

        ],
    ],

    'filters' => [

        'buttons' => [

            'remove' => [
                'label' => 'حذف فیلتر',
            ],

            'remove_all' => [
                'label' => 'حذف تمام فیلترها',
                'tooltip' => 'حذف تمام فیلترها',
            ],

            'reset' => [
                'label' => 'بازنشانی فیلترها',
            ],

        ],

        'indicator' => 'فیلترهای فعال',

        'multi_select' => [
            'placeholder' => 'همه',
        ],

        'select' => [
            'placeholder' => 'همه',
        ],

        'trashed' => [

            'label' => 'رکوردهای حذف‌شده',

            'only_trashed' => 'فقط رکوردهای حذف‌شده',

            'with_trashed' => 'به همراه رکوردهای حذف‌شده',

            'without_trashed' => 'بدون رکوردهای حذف‌شده',

        ],

    ],

    'reorder_indicator' => 'برای تغییر ترتیب بکشید و رها کنید.',

    'selection_indicator' => [

        'selected_count' => '1 آیتم انتخاب شده.|:count آیتم انتخاب شده.',

        'buttons' => [

            'select_all' => [
                'label' => 'انتخاب همه‌ی :count آیتم',
            ],

            'deselect_all' => [
                'label' => 'عدم انتخاب',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'مرتب‌سازی براساس',
            ],

            'direction' => [

                'label' => 'جهت مرتب‌سازی',

                'options' => [
                    'asc' => 'صعودی',
                    'desc' => 'نزولی',
                ],

            ],

        ],

    ],

];
