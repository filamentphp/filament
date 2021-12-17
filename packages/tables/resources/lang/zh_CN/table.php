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

        'overview' => '显示 :first 到 :last 的 :total 结果',

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

    'selection_indicator' => [

        'buttons' => [

            'select_all' => [
                'label' => '选择全部 :count 条记录',
            ],

        ],

    ],

];
