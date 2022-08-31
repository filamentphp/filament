<?php

return [

    'fields' => [

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

        'filter' => [
            'label' => 'فیلتر',
        ],

        'open_actions' => [
            'label' => 'بازکردن عملیات',
        ],

        'toggle_columns' => [
            'label' => 'باز/بستن ستون ها',
        ],

    ],

    'empty' => [
        'heading' => 'هیچ رکوردی یافت نشد',
    ],

    'filters' => [

        'buttons' => [

            'reset' => [
                'label' => 'حذف فیلترها',
            ],

        ],

        'multi_select' => [
            'placeholder' => 'همه',
        ],

        'select' => [
            'placeholder' => 'همه',
        ],

        'trashed' => [

            'label' => 'رکوردهای حذف شده',

            'only_trashed' => 'فقط رکوردهای حذف شده',

            'with_trashed' => 'به همراه رکوردهای حذف شده',

            'without_trashed' => 'بدون رکوردهای حذف شده',

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

];
