<?php

return [

    'column_toggle' => [

        'heading' => '显示字段',

    ],

    'columns' => [

        'text' => [

            'actions' => [
                'collapse_list' => '收起 :count 条记录',
                'expand_list' => '展示 :count 条记录',
            ],

            'more_list_items' => '还有 :count 条记录',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => '选择或取消选择所有数据。',
        ],

        'bulk_select_record' => [
            'label' => '选择或取消选择第 :key 条数据。',
        ],

        'bulk_select_group' => [
            'label' => '选择或取消选择 :title 分组数据。',
        ],

        'search' => [
            'label' => '搜索',
            'placeholder' => '搜索',
            'indicator' => '搜索',
        ],

    ],

    'summary' => [

        'heading' => '合计',

        'subheadings' => [
            'all' => '所有 :label',
            'group' => ':group 分组',
            'page' => '本页',
        ],

        'summarizers' => [

            'average' => [
                'label' => '平均',
            ],

            'count' => [
                'label' => '计数',
            ],

            'sum' => [
                'label' => '求和',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => '停止拖放排序',
        ],

        'enable_reordering' => [
            'label' => '开始拖放排序',
        ],

        'filter' => [
            'label' => '筛选',
        ],

        'group' => [
            'label' => '分组',
        ],

        'open_bulk_actions' => [
            'label' => '批量操作',
        ],

        'toggle_columns' => [
            'label' => '切换显示字段',
        ],

    ],

    'empty' => [

        'heading' => '没有 :model',

        'description' => '创建一条 :model',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => '确定',
            ],

            'remove' => [
                'label' => '取消筛选条件',
            ],

            'remove_all' => [
                'label' => '重置所有筛选条件',
                'tooltip' => '重置所有筛选条件',
            ],

            'reset' => [
                'label' => '重置',
            ],

        ],

        'heading' => '筛选条件',

        'indicator' => '激活筛选条件',

        'multi_select' => [
            'placeholder' => '所有',
        ],

        'select' => [
            'placeholder' => '所有',
        ],

        'trashed' => [

            'label' => '已删除记录',

            'only_trashed' => '仅显示已删除记录',

            'with_trashed' => '显示全部记录',

            'without_trashed' => '不显示已删除记录',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => '分组',
                'placeholder' => '分组',
            ],

            'direction' => [

                'label' => '分组排序',

                'options' => [
                    'asc' => '升序',
                    'desc' => '降序',
                ],

            ],

        ],

    ],

    'reorder_indicator' => '拖放记录进行排序。',

    'selection_indicator' => [

        'selected_count' => '已选择 1 条记录|已选择 :count 条记录',

        'actions' => [

            'select_all' => [
                'label' => '选择全部 :count 条记录',
            ],

            'deselect_all' => [
                'label' => '取消选择所有记录',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => '排序',
            ],

            'direction' => [

                'label' => '排序方式',

                'options' => [
                    'asc' => '升序',
                    'desc' => '降序',
                ],

            ],

        ],

    ],

];
