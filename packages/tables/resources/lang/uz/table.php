<?php

return [

    'column_toggle' => [

        'heading' => 'Ustunlar',

    ],

    'columns' => [

        'text' => [
            'more_list_items' => 'va :count ta yana',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Har qanday amal uchun barcha elementlarni belgilash/olib tashlash.',
        ],

        'bulk_select_record' => [
            'label' => 'Amal uchun :key elementini belgilash/olib tashlash.',
        ],

        'bulk_select_group' => [
            'label' => 'Guruh uchun :title ni belgilash/olib tashlash.',
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
            'all' => 'Barcha :label',
            'group' => ':group xulosasi',
            'page' => 'Ushbu sahifa',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'O\'rtacha',
            ],

            'count' => [
                'label' => 'Hisob',
            ],

            'sum' => [
                'label' => 'Yig\'indi',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Yozuvi tartibini yakunlash',
        ],

        'enable_reordering' => [
            'label' => 'Yozuvlarni tartiblash',
        ],

        'filter' => [
            'label' => 'Filtr',
        ],

        'group' => [
            'label' => 'Guruh',
        ],

        'open_bulk_actions' => [
            'label' => 'Ko\'p elementli amallar',
        ],

        'toggle_columns' => [
            'label' => 'Ustunlarni yoqish/yopish',
        ],

    ],

    'empty' => [

        'heading' => 'Hech qanday :model yo\'q',

        'description' => ':model yaratish uchun boshlang.',

    ],

    'filters' => [

        'actions' => [

            'remove' => [
                'label' => 'Filtrlarni o\'chirish',
            ],

            'remove_all' => [
                'label' => 'Barcha filtrlarni o\'chirish',
                'tooltip' => 'Barcha filtrlarni o\'chirish',
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

            'label' => 'O\'chirilgan yozuvlar',

            'only_trashed' => 'Faqat o\'chirilgan yozuvlar',

            'with_trashed' => 'O\'chirilgan yozuvlar bilan',

            'without_trashed' => 'O\'chirilgan yozuvsiz',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Guruhlash',
                'placeholder' => 'Guruhlash',
            ],

            'direction' => [

                'label' => 'Guruh tartibi',

                'options' => [
                    'asc' => 'O\'sish tartibida',
                    'desc' => 'Kamayish tartibida',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Yozuvlarnidrag and drop qilib tartiblang.',

    'selection_indicator' => [

        'selected_count' => '1 yozuv tanlandi|:count yozuv tanlandi',

        'actions' => [

            'select_all' => [
                'label' => 'Barchasini tanlash :count',
            ],

            'deselect_all' => [
                'label' => 'Barchasini tanlamaslik',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Saralash bo\'yicha',
            ],

            'direction' => [

                'label' => 'Saralash yo\'nalishi',

                'options' => [
                    'asc' => 'O\'sish',
                    'desc' => 'Kamayish',
                ],

            ],

        ],

    ],

];