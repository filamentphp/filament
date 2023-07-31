<?php

return [

    'columns' => [

        'tags' => [
            'more' => '... :count илүү',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Олонг сонгох/Цуцлах.',
        ],

        'bulk_select_record' => [
            'label' => 'Олонг сонгох/Цуцлах :key.',
        ],

        'search_query' => [
            'label' => 'Хайх',
            'placeholder' => 'Хайх',
        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Эрэмбэлэлтийг дуусгах',
        ],

        'enable_reordering' => [
            'label' => 'Мөрүүдийг эрэмбэлэх',
        ],

        'filter' => [
            'label' => 'Шүүлтүүр',
        ],

        'open_actions' => [
            'label' => 'Үйлдэл',
        ],

        'toggle_columns' => [
            'label' => 'Баганыг нээх/хаах',
        ],

    ],

    'empty' => [

        'heading' => 'Илэрц хоосон',

    ],

    'filters' => [

        'actions' => [

            'remove' => [
                'label' => 'Цэвэрлэх',
            ],

            'remove_all' => [
                'label' => 'Бүгдийг цэвэрлэх',
                'tooltip' => 'Бүгдийг цэвэрлэх',
            ],

            'reset' => [
                'label' => 'Филтерийг болиулах',
            ],

        ],

        'indicator' => 'Филтерийг идэвхижүүлэх',

        'multi_select' => [
            'placeholder' => 'Бүгд',
        ],

        'select' => [
            'placeholder' => 'Бүгд',
        ],

        'trashed' => [

            'label' => 'Хогийн сав',

            'only_trashed' => 'Зөвхөн устгасанг',

            'with_trashed' => 'Аль алиныг',

            'without_trashed' => 'Хэвийн',

        ],

    ],

    'reorder_indicator' => 'Чирж эрэмбэлэх.',

    'selection_indicator' => [

        'selected_count' => '1 бичлэг сонгогдов|:count -г сонгов',

        'actions' => [

            'select_all' => [
                'label' => 'Бүгдийг сонго :count',
            ],

            'deselect_all' => [
                'label' => 'Бүгдийг эс сонго',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Эрэмбэлэх',
            ],

            'direction' => [

                'label' => 'Эрэмбэлэх',

                'options' => [
                    'asc' => 'Өсөх',
                    'desc' => 'Буурах',
                ],

            ],

        ],

    ],

];
