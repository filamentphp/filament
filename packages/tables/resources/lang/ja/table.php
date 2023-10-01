<?php

return [

    'column_toggle' => [

        'heading' => 'Columns',

    ],

    'columns' => [

        'text' => [
            'more_list_items' => 'あと:count個あります',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Select/deselect all items for bulk actions.',
        ],

        'bulk_select_record' => [
            'label' => 'Select/deselect item :key for bulk actions.',
        ],

        'bulk_select_group' => [
            'label' => 'Select/deselect group :title for bulk actions.',
        ],

        'search' => [
            'label' => '検索',
            'placeholder' => '検索',
            'indicator' => '検索',
        ],

    ],

    'summary' => [

        'heading' => 'Summary',

        'subheadings' => [
            'all' => 'All :label',
            'group' => ':group summary',
            'page' => 'This page',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Average',
            ],

            'count' => [
                'label' => 'Count',
            ],

            'sum' => [
                'label' => 'Sum',
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
            'label' => '絞り込み',
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

        'description' => 'Create a :model to get started.',

    ],

    'filters' => [

        'actions' => [

            'remove' => [
                'label' => '絞り込みを解除',
            ],

            'remove_all' => [
                'label' => '全ての絞り込みを解除',
                'tooltip' => '全ての絞り込みを解除',
            ],

            'reset' => [
                'label' => 'リセット',
            ],

        ],

        'heading' => '絞り込み',

        'indicator' => '有効な絞り込み',

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
                'label' => 'Group by',
                'placeholder' => 'Group by',
            ],

            'direction' => [

                'label' => 'Group direction',

                'options' => [
                    'asc' => 'Ascending',
                    'desc' => 'Descending',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'ドラッグ＆ドロップでレコードを並び替え。',

    'selection_indicator' => [

        'selected_count' => '1件選択済み|:count件選択済み',

        'actions' => [

            'select_all' => [
                'label' => ':count件全て選択',
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

                'label' => '並び変え方向',

                'options' => [
                    'asc' => '昇順',
                    'desc' => '降順',
                ],

            ],

        ],

    ],

];
