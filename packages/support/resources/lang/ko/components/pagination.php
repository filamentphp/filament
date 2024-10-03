<?php

return [

    'label' => '페이지 탐색',

    'overview' => ':total 건 중 :first 건부터 :last 건까지 보기',

    'fields' => [

        'records_per_page' => [

            'label' => '페이지 당',

            'options' => [
                'all' => '전체',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => '첫 페이지',
        ],

        'go_to_page' => [
            'label' => ':page 페이지로 이동',
        ],

        'last' => [
            'label' => '마지막 페이지',
        ],

        'next' => [
            'label' => '다음',
        ],

        'previous' => [
            'label' => '이전',
        ],

    ],

];
