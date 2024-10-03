<?php

return [

    'label' => '分页',

    'overview' => '{1} 只有 1 条记录|[2,*] 当前显示第 :first 条到第 :last 条，共 :total 条',

    'fields' => [

        'records_per_page' => [

            'label' => '每页',

            'options' => [
                'all' => '所有',
            ],

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
