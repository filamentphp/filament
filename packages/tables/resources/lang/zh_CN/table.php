<?php

return [

    'fields' => [

        'search_query' => [
            'label' => '搜索',
            'placeholder' => '搜索',
        ],

    ],

    'pagination' => [

        'label' => '分页',

        'overview' => '当前显示第 :first 条到第 :last 条，共 :total 条',

        'fields' => [

            'records_per_page' => [
                'label' => '每页',
            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => '跳转到 :page',
            ],

            'next' => [
                'label' => '下一页',
            ],

            'previous' => [
                'label' => '上一页',
            ],

        ],

    ],

    'buttons' => [

        'filter' => [
            'label' => '筛选',
        ],

        'open_actions' => [
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

        'buttons' => [

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

        'buttons' => [

            'select_all' => [
                'label' => '选择全部 :count 条记录',
            ],

            'deselect_all' => [
                'label' => '取消全选',
            ],

        ],

    ],

];
