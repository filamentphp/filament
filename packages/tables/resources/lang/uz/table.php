<?php

return [

    'column_manager' => [

        'heading' => 'Ustunlar',

    ],

    'columns' => [

        'text' => [

            'actions' => [
                'collapse_list' => ':counttadan kamroq ko\'rsatish',
                'expand_list' => ':counttadan ko\'proq ko\'rsatish',
            ],

            'more_list_items' => 'va :counttadan ko\'proq',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Ommaviy amallar uchun tanlash/bekor qilish.',
        ],

        'bulk_select_record' => [
            'label' => ':key elementi ommaviy harakatlari uchun tanlash/bekor qilish.',
        ],

        'bulk_select_group' => [
            'label' => 'Sarlavha guruhi uchun tanlash/bekor qilish.',
        ],

        'search' => [
            'label' => 'Qidirish',
            'placeholder' => 'Qidirish',
            'indicator' => 'Qidirish',
        ],

    ],

    'summary' => [

        'heading' => 'Xulosa',

        'subheadings' => [
            'all' => 'Barcha :labellar',
            'group' => ':group xulosa',
            'page' => 'Ushbu sahifa',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'O\'rtacha',
            ],

            'count' => [
                'label' => 'Hisoblash',
            ],

            'sum' => [
                'label' => 'Umumiy qiymat (SUM)',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Yozuvlarni qayta tartiblashni tugatish',
        ],

        'enable_reordering' => [
            'label' => 'Yozuvlarni qayta tartiblash',
        ],

        'filter' => [
            'label' => 'Filtrlash',
        ],

        'group' => [
            'label' => 'Guruh',
        ],

        'open_bulk_actions' => [
            'label' => 'Ommaviy amallar',
        ],

        'column_manager' => [
            'label' => 'Ustunlarni almashtirish',
        ],

    ],

    'empty' => [

        'heading' => ':model mavjud emas',

        'description' => 'Boshlash uchun :model yarating',

    ],

    'filters' => [

        'actions' => [

            'remove' => [
                'label' => 'Filtrni o\'chirish',
            ],

            'remove_all' => [
                'label' => 'Barcha filtrlarni olib tashlash',
                'tooltip' => 'Barcha filtrlarni olib tashlash',
            ],

            'reset' => [
                'label' => 'Qayta o\'rnatish',
            ],

        ],

        'heading' => 'Filtrlar',

        'indicator' => 'Faol filtrlar',

        'multi_select' => [
            'placeholder' => 'Barchasi',
        ],

        'select' => [
            'placeholder' => 'Barchasi',
        ],

        'trashed' => [

            'label' => 'O\'chirilgan ma\'lumotlar',

            'only_trashed' => 'Faqat o\'chirilgan ma\'lumotlar',

            'with_trashed' => 'O\'chirilgan ma\'lumotlar bilan',

            'without_trashed' => 'O\'chirilgan ma\'lumotlarsiz',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Guruhlash',
                'placeholder' => 'Guruhlash',
            ],

            'direction' => [

                'label' => 'Guruh yo\'nalishi',

                'options' => [
                    'asc' => 'Ko\'tarilish (ASC)',
                    'desc' => 'Tushish (DESC)',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Ma\'lumotlarni tartibda sudrab olib tashlang.',

    'selection_indicator' => [

        'selected_count' => '1ta ma\'lumot tanlangan|:countta ma\'lumotlar tanlangan',

        'actions' => [

            'select_all' => [
                'label' => ':count - barchasini belgilash',
            ],

            'deselect_all' => [
                'label' => 'Barchasini belgilamaslik',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Saralash',
            ],

            'direction' => [

                'label' => 'Saralash',

                'options' => [
                    'asc' => 'Ko\'tarilish (ASC)',
                    'desc' => 'Tushish (DESC)',
                ],

            ],

        ],

    ],

];
