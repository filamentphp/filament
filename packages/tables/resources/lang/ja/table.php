<?php

return [

    'fields' => [

        'search_query' => [
            'label' => '検索',
            'placeholder' => '検索',
        ],

    ],

    'pagination' => [

        'label' => 'ページネーション',

        'overview' => ':total件中:first件目から:last件目を表示',

        'fields' => [

            'records_per_page' => [
                'label' => '件を表示',
            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => ':pageページへ移動',
            ],

            'next' => [
                'label' => '次',
            ],

            'previous' => [
                'label' => '前',
            ],

        ],

    ],

    'buttons' => [

        'filter' => [
            'label' => '絞り込み',
        ],

        'open_actions' => [
            'label' => '操作を開く',
        ],

        'toggle_columns' => [
            'label' => '列を切り替える',
        ],

    ],

    'empty' => [
        'heading' => 'レコードが見つかりません',
    ],

    'filters' => [

        'buttons' => [

            'reset' => [
                'label' => 'リセット',
            ],

        ],

        'multi_select' => [
            'placeholder' => '全件',
        ],

        'select' => [
            'placeholder' => '全件',
        ],

    ],

    'selection_indicator' => [

        'selected_count' => '1 record selected.|:count records selected.',

        'buttons' => [

            'select_all' => [
                'label' => 'Select all :count',
            ],

            'deselect_all' => [
                'label' => 'Deselect all',
            ],

        ],

    ],

];
