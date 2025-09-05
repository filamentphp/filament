<?php

return [

    'label' => '分頁導航',

    'overview' => '正在顯示第 :first 至 :last 項結果，共 :total 項',

    'fields' => [

        'records_per_page' => [

            'label' => '每頁顯示',

            'options' => [
                'all' => '全部',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => '第一頁',
        ],

        'go_to_page' => [
            'label' => '前往第 :page 頁',
        ],

        'last' => [
            'label' => '最後一頁',
        ],

        'next' => [
            'label' => '下一頁',
        ],

        'previous' => [
            'label' => '上一頁',
        ],

    ],

];
