<?php

return [

    'fields' => [

        'search_query' => [
            'label' => '搜尋',
            'placeholder' => '搜尋',
        ],

    ],

    'pagination' => [

        'label' => '分頁導航',

        'overview' => '正在顯示第 :first 至 :last 項結果，共 :total 項',

        'fields' => [

            'records_per_page' => [
                'label' => '每頁顯示',
            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => '前往第 :page 頁',
            ],

            'next' => [
                'label' => '下一頁',
            ],

            'previous' => [
                'label' => '上一頁',
            ],

        ],

    ],

    'buttons' => [

        'filter' => [
            'label' => '篩選',
        ],

        'open_actions' => [
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

        'buttons' => [

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

        'selected_count' => '已選擇 :count 個項目。',

        'buttons' => [

            'select_all' => [
                'label' => '選擇全部 :count 項',
            ],

            'deselect_all' => [
                'label' => '取消選擇全部',
            ],

        ],

    ],

];
