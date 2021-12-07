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
            'label' => 'Open actions',
        ],

    ],

    'actions' => [

        'modal' => [

            'requires_confirmation_subheading' => 'Are you sure you would like to do this?',

            'buttons' => [

                'cancel' => [
                    'label' => '取消',
                ],

                'confirm' => [
                    'label' => 'Confirm',
                ],

                'submit' => [
                    'label' => 'Submit',
                ],

            ],

        ],

        'buttons' => [

            'select_all' => [
                'label' => 'Select all :count records',
            ],

        ],

    ],

    'empty' => [
        'heading' => '没有找到相关记录',
    ],

];
