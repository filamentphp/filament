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

    ],

    'actions' => [

        'modal' => [

            'requires_confirmation_subheading' => '您确定要这样做吗？',

            'buttons' => [

                'cancel' => [
                    'label' => '取消',
                ],

                'confirm' => [
                    'label' => '确认',
                ],

                'submit' => [
                    'label' => '提交',
                ],

            ],

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

            'close' => [
                'label' => '关闭',
            ],

        ],

        'multi_select' => [
            'placeholder' => '全部',
        ],

        'select' => [
            'placeholder' => '全部',
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
