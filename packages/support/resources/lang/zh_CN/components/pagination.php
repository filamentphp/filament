<?php

return [

    'label' => '分页',

    'overview' => '当前显示第 :first 条到第 :last 条，共 :total 条',

    'fields' => [

        'records_per_page' => [
            'label' => '每页',
        ],

    ],

    'actions' => [

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

];
