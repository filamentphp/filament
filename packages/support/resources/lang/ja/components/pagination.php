<?php

return [

    'label' => 'ページネーション',

    'overview' => ':total件中:first件目から:last件目を表示',

    'fields' => [

        'records_per_page' => [

            'label' => '件を表示',

            'options' => [
                'all' => 'すべて',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => '最初',
        ],

        'go_to_page' => [
            'label' => ':pageページへ移動',
        ],

        'last' => [
            'label' => '最後',
        ],

        'next' => [
            'label' => '次',
        ],

        'previous' => [
            'label' => '前',
        ],

    ],

];
