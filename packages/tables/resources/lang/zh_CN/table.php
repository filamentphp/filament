<?php

return [

    'fields' => [

        'search' => [
            'label' => '搜索',
            'placeholder' => '搜索',
        ],

    ],

    'actions' => [

        'filter' => [
            'label' => '筛选',
        ],

        'open_bulk_actions' => [
            'label' => '展开操作项',
        ],

        'toggle_columns' => [
            'label' => '切换显示列',
        ],

    ],

    'empty' => [
        'heading' => '没有找到相关记录',
    ],

    'filters' => [

        'actions' => [

            'reset' => [
                'label' => '重置筛选条件',
            ],

        ],

        'multi_select' => [
            'placeholder' => '全部',
        ],

        'select' => [
            'placeholder' => '全部',
        ],

        'trashed' => [

            'label' => '已删除记录',

            'only_trashed' => '仅显示已删除记录',

            'with_trashed' => '显示全部记录',

            'without_trashed' => '不显示已删除记录',

        ],

    ],

    'selection_indicator' => [

        'selected_count' => '已选 :count 条记录',

        'actions' => [

            'select_all' => [
                'label' => '选择全部 :count 条记录',
            ],

            'deselect_all' => [
                'label' => '取消全选',
            ],

        ],

    ],

];
