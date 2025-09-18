<?php

return [

    'column_manager' => [

        'heading' => '欄位管理',

        'actions' => [

            'apply' => [
                'label' => '套用欄位設定',
            ],

            'reset' => [
                'label' => '重設',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => '操作',
        ],

        'select' => [

            'loading_message' => '載入中...',

            'no_search_results_message' => '無符合選項',

            'placeholder' => '請選擇',

            'searching_message' => '搜尋中...',

            'search_prompt' => '輸入關鍵字搜尋...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => '收起 :count 項',
                'expand_list' => '展開 :count 項',
            ],

            'more_list_items' => '及其他 :count 項',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => '全選/取消全選本頁項目',
        ],

        'bulk_select_record' => [
            'label' => '選取/取消選取項目 :key',
        ],

        'bulk_select_group' => [
            'label' => '選取/取消選取群組 :title',
        ],

        'search' => [
            'label' => '搜尋',
            'placeholder' => '請輸入關鍵字',
            'indicator' => '搜尋',
        ],

    ],

    'summary' => [

        'heading' => '統計摘要',

        'subheadings' => [
            'all' => '全部 :label',
            'group' => ':group 群組統計',
            'page' => '本頁資料',
        ],

        'summarizers' => [

            'average' => [
                'label' => '平均值',
            ],

            'count' => [
                'label' => '計數',
            ],

            'sum' => [
                'label' => '加總',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => '完成排序',
        ],

        'enable_reordering' => [
            'label' => '調整排序',
        ],

        'filter' => [
            'label' => '篩選',
        ],

        'group' => [
            'label' => '群組',
        ],

        'open_bulk_actions' => [
            'label' => '批次操作',
        ],

        'column_manager' => [
            'label' => '欄位管理',
        ],

    ],

    'empty' => [

        'heading' => '暫無 :model',

        'description' => '請建立:model以開始使用',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => '套用篩選',
            ],

            'remove' => [
                'label' => '移除篩選',
            ],

            'remove_all' => [
                'label' => '清除所有篩選',
                'tooltip' => '清除所有篩選條件',
            ],

            'reset' => [
                'label' => '重設',
            ],

        ],

        'heading' => '篩選條件',

        'indicator' => '已啟用篩選',

        'multi_select' => [
            'placeholder' => '全部',
        ],

        'select' => [

            'placeholder' => '全部',

            'relationship' => [
                'empty_option_label' => '無關聯項目',
            ],

        ],

        'trashed' => [

            'label' => '已刪除記錄',

            'only_trashed' => '僅顯示已刪除',

            'with_trashed' => '包含已刪除',

            'without_trashed' => '不含已刪除',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => '群組依據',
            ],

            'direction' => [

                'label' => '排序方向',

                'options' => [
                    'asc' => '升冪',
                    'desc' => '降冪',
                ],

            ],

        ],

    ],

    'reorder_indicator' => '拖曳項目調整排序',

    'selection_indicator' => [

        'selected_count' => '已選取 :count 筆記錄',

        'actions' => [

            'select_all' => [
                'label' => '全選 :count 筆',
            ],

            'deselect_all' => [
                'label' => '取消全選',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => '排序欄位',
            ],

            'direction' => [

                'label' => '排序方式',

                'options' => [
                    'asc' => '升冪',
                    'desc' => '降冪',
                ],

            ],

        ],

    ],

    'default_model_label' => '記錄',

];
