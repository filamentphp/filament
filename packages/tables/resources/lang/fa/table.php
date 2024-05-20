<?php

return [

    'column_toggle' => [

        'heading' => 'ستون‌ها',

    ],

    'columns' => [

        'text' => [

            'actions' => [
                'collapse_list' => 'نمایش :count کمتر',
                'expand_list' => 'نمایش :count بیشتر',
            ],

            'more_list_items' => 'و :count تا بیشتر',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'انتخاب / عدم‌انتخاب تمامی موارد برای اقدامات گروهی',
        ],

        'bulk_select_record' => [
            'label' => 'انتخاب / عدم‌انتخاب مورد :key برای اقدامات گروهی',
        ],

        'bulk_select_group' => [
            'label' => 'انتخاب / عدم‌انتخاب گروه :title برای اقدامات گروهی.',
        ],

        'search' => [
            'label' => 'جستجو',
            'placeholder' => 'جستجو',
            'indicator' => 'جستجو',
        ],

    ],

    'summary' => [

        'heading' => 'خلاصه',

        'subheadings' => [
            'all' => 'تمام :label',
            'group' => ':group خلاصه',
            'page' => 'این صفحه',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'میانگین',
            ],

            'count' => [
                'label' => 'تعداد',
            ],

            'sum' => [
                'label' => 'مجموع',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'اتمام بازچینش رکوردها',
        ],

        'enable_reordering' => [
            'label' => 'بازچینش رکوردها',
        ],

        'filter' => [
            'label' => 'فیلتر',
        ],

        'group' => [
            'label' => 'گروه',
        ],

        'open_bulk_actions' => [
            'label' => 'عملیات گروهی',
        ],

        'toggle_columns' => [
            'label' => 'باز / بستن ستون‌ها',
        ],

    ],

    'empty' => [

        'heading' => ':model یافت نشد.',

        'description' => 'برای شروع یک :model ایجاد کنید.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'اعمال فیلترها',
            ],

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

        'heading' => 'فیلترها',

        'indicator' => 'فیلترهای فعال',

        'multi_select' => [
            'placeholder' => 'همه',
        ],

        'select' => [
            'placeholder' => 'همه',
        ],

        'trashed' => [

            'label' => 'رکوردهای حذف‌‌شده',

            'only_trashed' => 'فقط رکوردهای حذف‌‌شده',

            'with_trashed' => 'به همراه رکوردهای حذف‌‌شده',

            'without_trashed' => 'بدون رکوردهای حذف‌‌شده',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'گروه‌بندی براساس',
                'placeholder' => 'گروه‌بندی براساس',
            ],

            'direction' => [

                'label' => 'ترتیب گروه',

                'options' => [
                    'asc' => 'صعودی',
                    'desc' => 'نزولی',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'برای تغییر ترتیب، بکشید و رها کنید.',

    'selection_indicator' => [

        'selected_count' => '1 آیتم انتخاب شده|:count آیتم انتخاب شده',

        'actions' => [

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
                'label' => 'مرتب‌ سازی براساس',
            ],

            'direction' => [

                'label' => 'جهت مرتب‌ سازی',

                'options' => [
                    'asc' => 'صعودی',
                    'desc' => 'نزولی',
                ],

            ],

        ],

    ],

];
