<?php

return [

    'fields' => [

        'search' => [
            'label' => '搜尋',
            'placeholder' => '搜尋',
        ],

    ],

    'actions' => [

        'filter' => [
            'label' => '篩選',
        ],

        'open_bulk_actions' => [
            'label' => '打開動作',
        ],

        'toggle_columns' => [
            'label' => '顯示／隱藏直列',
        ],

    ],

    'empty' => [
        'heading' => '未找到資料',
    ],

    'filters' => [

        'actions' => [

            'reset' => [
                'label' => '重設篩選',
            ],

        ],

        'multi_select' => [
            'placeholder' => '全部',
        ],

        'select' => [
            'placeholder' => '全部',
        ],

        'trashed' => [

            'label' => '已刪除的資料',

            'only_trashed' => '僅顯示已刪除的資料',

            'with_trashed' => '包含已刪除的資料',

            'without_trashed' => '不含已刪除的資料',

        ],

    ],

    'selection_indicator' => [

        'selected_count' => '已選擇 :count 個項目',

        'actions' => [

            'select_all' => [
                'label' => '選擇全部 :count 項',
            ],

            'deselect_all' => [
                'label' => '取消選擇全部',
            ],

        ],

    ],

];
