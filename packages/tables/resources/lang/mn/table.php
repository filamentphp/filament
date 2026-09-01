<?php

return [

    'column_manager' => [

        'heading' => 'Баганууд',

    ],

    'columns' => [

        'text' => [

            'actions' => [
                'collapse_list' => 'Харуулах :count бага',
                'expand_list' => 'Харуулах :count илүү',
            ],

            'more_list_items' => 'ба :count илүү',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Олонг сонгох/Цуцлах.',
        ],

        'bulk_select_record' => [
            'label' => 'Олонг сонгох/Цуцлах :key.',
        ],

        'bulk_select_group' => [
            'label' => 'Сонгох/цуцлах бүлэг :title багц үйлдэлд.',
        ],

        'search' => [
            'label' => 'Хайх',
            'placeholder' => 'Хайх',
            'indicator' => 'Хайх',
        ],

    ],

    'summary' => [

        'heading' => 'Нийлбэр',

        'subheadings' => [
            'all' => 'Бүгд :label',
            'group' => ':group нийлбэр',
            'page' => 'Энэ хуудас',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Дундаж',
            ],

            'count' => [
                'label' => 'Тоо',
            ],

            'sum' => [
                'label' => 'Нийлбэр',
            ],

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

        'group' => [
            'label' => 'Бүлэг',
        ],

        'open_bulk_actions' => [
            'label' => 'Багц үйлдэл',
        ],

        'column_manager' => [
            'label' => 'Баганыг нээх/хаах',
        ],

    ],

    'empty' => [

        'heading' => ':model хоосон',

        'description' => 'Шинэ :model мэдээлэл үүсгэх.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Шүүлтийг батлах',
            ],

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

        'heading' => 'Шүүлтүүрүүд',

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

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Бүлэглэх',
                'placeholder' => 'Бүлэглэх',
            ],

            'direction' => [

                'label' => 'Бүлгийн чиглэл',

                'options' => [
                    'asc' => 'Өсөх',
                    'desc' => 'Буурах',
                ],

            ],

        ],

    ],

];
