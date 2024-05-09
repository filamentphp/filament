<?php

return [

    'column_toggle' => [

        'heading' => 'カラム',

    ],

    'columns' => [

        'text' => [

            'actions' => [
                'collapse_list' => ':count件非表示',
                'expand_list' => ':count件表示',
            ],

            'more_list_items' => 'あと:count件あります',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => '一括操作の全項目の選択/解除。',
        ],

        'bulk_select_record' => [
            'label' => '一括操作のキー:keyの選択/解除。',
        ],

        'bulk_select_group' => [
            'label' => '一括操作のグループ:keyの選択/解除。',
        ],

        'search' => [
            'label' => '検索',
            'placeholder' => '検索',
            'indicator' => '検索',
        ],

    ],

    'summary' => [

        'heading' => 'サマリー',

        'subheadings' => [
            'all' => 'すべての:label',
            'group' => ':groupのサマリー',
            'page' => 'このページ',
        ],

        'summarizers' => [

            'average' => [
                'label' => '平均',
            ],

            'count' => [
                'label' => 'カウント',
            ],

            'sum' => [
                'label' => '合計',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'レコードの並び替えを終了',
        ],

        'enable_reordering' => [
            'label' => 'レコードの並び替え',
        ],

        'filter' => [
            'label' => 'フィルタ',
        ],

        'group' => [
            'label' => 'グループ',
        ],

        'open_bulk_actions' => [
            'label' => '操作を開く',
        ],

        'toggle_columns' => [
            'label' => '列を切り替える',
        ],

    ],

    'empty' => [

        'heading' => ':modelが見つかりません',

        'description' => ':modelを作成してください。',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'フィルタを適用',
            ],

            'remove' => [
                'label' => 'フィルタを解除',
            ],

            'remove_all' => [
                'label' => 'すべてのフィルタを解除',
                'tooltip' => 'すべてのフィルタを解除',
            ],

            'reset' => [
                'label' => 'リセット',
            ],

        ],

        'heading' => 'フィルタ',

        'indicator' => '有効なフィルタ',

        'multi_select' => [
            'placeholder' => '全件',
        ],

        'select' => [
            'placeholder' => '全件',
        ],

        'trashed' => [

            'label' => '削除済みレコード',

            'only_trashed' => '削除済みレコードのみ',

            'with_trashed' => '削除済みレコード含む',

            'without_trashed' => '削除済みレコードを除く',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'グループ化',
                'placeholder' => 'グループ化',
            ],

            'direction' => [

                'label' => 'グループ順の方向',

                'options' => [
                    'asc' => '昇順',
                    'desc' => '降順',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'ドラッグ＆ドロップでレコードを並び替え。',

    'selection_indicator' => [

        'selected_count' => '1件選択済み|:count件選択済み',

        'actions' => [

            'select_all' => [
                'label' => ':count件すべて選択',
            ],

            'deselect_all' => [
                'label' => '全選択解除',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => '並び順',
            ],

            'direction' => [

                'label' => '並び変えの方向',

                'options' => [
                    'asc' => '昇順',
                    'desc' => '降順',
                ],

            ],

        ],

    ],

];
